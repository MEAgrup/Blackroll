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

/**
 * Prove three.js only ever loads for a visitor of the Product page.
 *
 * esbuild's code-splitting extracts a dynamically-imported dependency (three)
 * into its own shared chunk file, named by content hash — not by whichever
 * entry imports it — so a naive "does this output's filename contain
 * product-3d" check flags that chunk as a false positive even though nothing
 * else references it. The correct check is reachability: walk the `imports`
 * graph from every entry point that is NOT product-3d, and fail only if
 * three.js is reachable from one of those.
 *
 * @param {import('esbuild').Metafile} metafile
 * @param {Record<string,string>} entryMap
 */
function assertThreeIsolatedToProduct( metafile, entryMap ) {
	const outputs = metafile.outputs;
	const outputByEntry = new Map();
	for ( const [ file, meta ] of Object.entries( outputs ) ) {
		if ( meta.entryPoint ) {
			outputByEntry.set( meta.entryPoint, file );
		}
	}

	function reachableFiles( startFile ) {
		const seen = new Set();
		const stack = [ startFile ];
		while ( stack.length ) {
			const file = stack.pop();
			if ( seen.has( file ) || ! outputs[ file ] ) {
				continue;
			}
			seen.add( file );
			for ( const imp of outputs[ file ].imports || [] ) {
				stack.push( imp.path );
			}
		}
		return seen;
	}

	for ( const [ name, entrySrc ] of Object.entries( entryMap ) ) {
		if ( name === 'product-3d' ) {
			continue;
		}
		const outFile = outputByEntry.get( entrySrc );
		if ( ! outFile ) {
			continue;
		}
		for ( const file of reachableFiles( outFile ) ) {
			const inputs = Object.keys( outputs[ file ].inputs || {} );
			if ( inputs.some( ( p ) => /node_modules\/three\//.test( p ) ) ) {
				throw new Error( `three.js leaked into the "${ name }" bundle via ${ file }` );
			}
		}
	}
}

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
		assertThreeIsolatedToProduct( result.metafile, entries );
		console.log( 'OK: three.js is isolated to the product-3d bundle.' );
	}
}

run().catch( ( err ) => {
	console.error( err );
	process.exit( 1 );
} );

// Touch readFileSync import so linters don't flag it if unused in future edits.
void readFileSync;
