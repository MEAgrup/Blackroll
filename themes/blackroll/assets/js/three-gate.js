/*
 * Shared 3D capability gate (home hero + Product page). Accessibility /
 * bandwidth safety net — NOT part of the client's accepted performance
 * trade-off, so it always applies:
 *   - prefers-reduced-motion → no 3D
 *   - no WebGL               → no 3D
 *   - 2G/3G or Save-Data     → no 3D
 * On any "no" the static fallback in the markup stays and three.js is never fetched.
 */
export function canRun3D() {
	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return false;
	}
	try {
		const canvas = document.createElement( 'canvas' );
		if ( ! ( canvas.getContext( 'webgl2' ) || canvas.getContext( 'webgl' ) ) ) {
			return false;
		}
	} catch ( e ) {
		return false;
	}
	const conn = navigator.connection;
	if ( conn && ( conn.saveData || /2g|3g/.test( conn.effectiveType || '' ) ) ) {
		return false;
	}
	return true;
}

/** Run `fn` once the browser is idle (after LCP), with a timeout fallback. */
export function whenIdle( fn ) {
	if ( 'requestIdleCallback' in window ) {
		window.requestIdleCallback( fn, { timeout: 1500 } );
	} else {
		setTimeout( fn, 300 );
	}
}
