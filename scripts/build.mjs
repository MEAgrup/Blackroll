/*
 * Minimal esbuild pipeline (PATCH-14 / Module 2).
 * - Bundles the small vanilla-JS entries (motion, selector, portfolio, contact).
 * - Keeps product-3d.js as an ISOLATED entry so the bundle analyzer can prove
 *   three.js never leaks into non-Product bundles.
 * - `--analyze` prints esbuild's metafile analysis.
 * - `--watch` rebuilds on change.
 *
 * The theme also works un-bundled in dev (files are enqueued directly); this
 * pipeline is for production minification + the analyzer gate.
 */
import { build, context } from 'esbuild';
import { readFileSync } from 'node:fs';

const analyze = process.argv.includes( '--analyze' );
const watch = process.argv.includes( '--watch' );

const entries = {
	motion: 'themes/blackroll/assets/js/motion.js',
	selector: 'themes/blackroll/assets/js/selector.js',
	portfolio: 'themes/blackroll/assets/js/portfolio.js',
	contact: 'themes/blackroll/assets/js/contact.js',
	// Isolated on purpose — Product-page-only 3D chunk (PATCH-17).
	'product-3d': 'themes/blackroll/assets/js/product-3d.js',
};

const options = {
	entryPoints: entries,
	outdir: 'themes/blackroll/assets/js/dist',
	bundle: true,
	minify: true,
	sourcemap: true,
	format: 'esm',
	splitting: true,
	target: [ 'es2018' ],
	metafile: true,
	logLevel: 'info',
};

async function run() {
	if ( watch ) {
		const ctx = await context( options );
		await ctx.watch();
		console.log( 'esbuild watching…' );
		return;
	}
	const result = await build( options );
	if ( analyze && result.metafile ) {
		const { analyzeMetafile } = await import( 'esbuild' );
		console.log( await analyzeMetafile( result.metafile ) );
		// Guard: fail if three.js ever appears outside the product-3d chunk.
		for ( const [ file, meta ] of Object.entries( result.metafile.outputs ) ) {
			if ( /three/.test( JSON.stringify( meta.inputs ) ) && ! /product-3d/.test( file ) ) {
				throw new Error( `three.js leaked into non-Product bundle: ${ file }` );
			}
		}
	}
}

run().catch( ( err ) => {
	console.error( err );
	process.exit( 1 );
} );

// Touch readFileSync import so linters don't flag it if unused in future edits.
void readFileSync;
