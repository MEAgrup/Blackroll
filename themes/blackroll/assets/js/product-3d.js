/*
 * Product-page 3D showcase — CODE-SPLIT STUB (Module 2, PATCH-17).
 *
 * Three.js is Product-page-only, bundled locally via npm (never a CDN), and
 * dynamically imported ONLY after the capability + connection gate passes.
 * Launch ships with the Lottie/static fallback (Module 4 decision gate); this
 * file is the isolated entry the bundle analyzer checks is absent from every
 * non-Product page bundle.
 *
 * Gate: WebGL context available AND not on a 2G/3G connection (where the
 * Network Information API is supported) AND not prefers-reduced-motion.
 */
export function canRun3D() {
	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return false;
	}
	try {
		var canvas = document.createElement( 'canvas' );
		var gl = canvas.getContext( 'webgl' ) || canvas.getContext( 'experimental-webgl' );
		if ( ! gl ) {
			return false;
		}
	} catch ( e ) {
		return false;
	}
	var conn = navigator.connection;
	if ( conn && /2g|3g/.test( conn.effectiveType || '' ) ) {
		return false;
	}
	return true;
}

export async function mount3D( container ) {
	if ( ! container || ! canRun3D() ) {
		return; // Keep the Lottie/static fallback in place.
	}
	// Post-launch (Module 4 gate): dynamically import three and build the scene.
	// const THREE = await import( 'three' );
	// ...code-split scene setup, validated against LCP < 2.5s before enabling.
}
