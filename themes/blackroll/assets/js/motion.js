/*
 * Blackroll motion loader (Module 2).
 * - prefers-reduced-motion: skip all motion, show static fallback frames.
 * - Lottie: lazy-init via IntersectionObserver (~150ms before in view).
 * - Three.js is Product-page-only and lives in its own code-split chunk
 *   (assets/js/product-3d.js), never loaded here.
 *
 * Markup contract:
 *   <div class="blackroll-lottie" data-lottie="/path/to/anim.json"
 *        data-loop="true">
 *     <img class="blackroll-lottie__fallback" src="static.webp" alt="…">
 *   </div>
 * The <img> fallback is real content for SEO/reduced-motion and is replaced
 * by the animation only when motion is allowed and the player is available.
 */
( function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function initLottie( el ) {
		var src = el.getAttribute( 'data-lottie' );
		if ( ! src || ! window.lottie ) {
			return; // No player bundled yet → keep the static fallback image.
		}
		var mount = document.createElement( 'div' );
		mount.className = 'blackroll-lottie__canvas';
		el.appendChild( mount );
		try {
			window.lottie.loadAnimation( {
				container: mount,
				renderer: 'svg',
				loop: el.getAttribute( 'data-loop' ) === 'true',
				autoplay: true,
				path: src,
			} );
			var fb = el.querySelector( '.blackroll-lottie__fallback' );
			if ( fb ) {
				fb.style.display = 'none';
			}
		} catch ( e ) {
			/* Leave fallback in place on any failure. */
		}
	}

	function boot() {
		var nodes = document.querySelectorAll( '.blackroll-lottie[data-lottie]' );
		if ( ! nodes.length || reduce ) {
			return; // Reduced motion or nothing to animate → static frames stay.
		}
		if ( ! ( 'IntersectionObserver' in window ) ) {
			nodes.forEach( initLottie );
			return;
		}
		var io = new IntersectionObserver(
			function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						initLottie( entry.target );
						obs.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '150px 0px' }
		);
		nodes.forEach( function ( n ) {
			io.observe( n );
		} );
	}

	if ( document.readyState !== 'loading' ) {
		boot();
	} else {
		document.addEventListener( 'DOMContentLoaded', boot );
	}
}() );
