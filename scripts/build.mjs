/*
 * Minimal esbuild pipeline (PATCH-14 / Module 2).
 * - Bundles the small vanilla-JS entries (motion, selector, portfolio, contact).
 * - Keeps product-3d.js and home-hero-3d.js as ISOLATED entries so the bundle
 *   analyzer can prove three.js never leaks into any other bundle.
 * - `--analyze` prints esbuild's metafile analysis.
 * - `--watch` rebuilds on change.
 *
 * The theme also works un-bundled in dev (files are enqueued directly); this
 * pipeline is for production minification + the analyzer gate.
 */
import { build, context } from 'esbuild';
import { readFileSync, rmSync } from 'node:fs';

const analyze = process.argv.includes( '--analyze' );
const watch = process.argv.includes( '--watch' );

const entries = {
	motion: 'themes/blackroll/assets/js/motion.js',
	selector: 'themes/blackroll/assets/js/selector.js',
	portfolio: 'themes/blackroll/assets/js/portfolio.js',
	contact: 'themes/blackroll/assets/js/contact.js',
	// Isolated on purpose — the only entries allowed to reach three.js
	// (PATCH-17, extended in Fase 2 to the homepage hero).
	'product-3d': 'themes/blackroll/assets/js/product-3d.js',
	'home-hero-3d': 'themes/blackroll/assets/js/home-hero-3d.js',
};

// Entries that may (lazily) load three.js; every other bundle must not reach it.
const THREE_ALLOWED = new Set( [ 'product-3d', 'home-hero-3d' ] );

const options = {
	entryPoints: entries,
	outdir: 'themes/blackroll/assets/js/dist',
	bundle: true,
	minify: true,
	// Built files are committed (Hostinger has no Node build step), so no
	// sourcemaps: they would be git-ignored and 404 in production.
	sourcemap: false,
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
		if ( THREE_ALLOWED.has( name ) ) {
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
	// dist/ is committed: wipe it first so stale content-hashed chunks from a
	// previous build never linger in the repo.
	rmSync( options.outdir, { recursive: true, force: true } );
	const result = await build( options );
	if ( analyze && result.metafile ) {
		const { analyzeMetafile } = await import( 'esbuild' );
		console.log( await analyzeMetafile( result.metafile ) );
		assertThreeIsolatedToProduct( result.metafile, entries );
		console.log( 'OK: three.js is isolated to the 3D bundles (product-3d, home-hero-3d).' );
	}
}

run().catch( ( err ) => {
	console.error( err );
	process.exit( 1 );
} );

// Touch readFileSync import so linters don't flag it if unused in future edits.
void readFileSync;
