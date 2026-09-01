/*
 * Blackroll motion loader (Module 2, extended for the "premium motion" hero —
 * client decision 2026-09: prioritize a premium look over the loading-speed
 * score on the homepage/Product page; see deploy/QA-CHECKLIST.md).
 *
 * This file is an ES module (bundled by scripts/build.mjs — `lottie-web` is a
 * bare import, it does not resolve unbundled in a browser) so it is only
 * enqueued when the built `dist/motion.js` exists (see enqueue.php). If it
 * isn't built yet, nothing loads and every fallback below (poster image,
 * static Lottie frame, no-video) stays in place — same "progressive" pattern
 * already used for EN content in this codebase.
 *
 * - prefers-reduced-motion: skip all motion, show static fallback frames.
 * - Lottie: lazy-init via IntersectionObserver (~150ms before in view), real
 *   player bundled via `lottie-web` (light/SVG build — no canvas/html renderer).
 * - Hero video: only starts if motion is allowed AND the connection isn't
 *   flagged slow/metered; otherwise the `poster` image is what visitors see
 *   (this is an accessibility/bandwidth safety net, not part of the client's
 *   accepted performance trade-off — it always applies).
 * - Three.js is Product-page-only and lives in its own code-split chunk
 *   (assets/js/product-3d.js), never loaded here.
 *
 * Markup contracts:
 *   <div class="blackroll-lottie" data-lottie="/path/to/anim.json" data-loop="true">
 *     <img class="blackroll-lottie__fallback" src="static.webp" alt="…">
 *   </div>
 *
 *   <video class="blackroll-hero__video" data-blackroll-hero-video
 *          data-src="/path/to/hero.mp4" muted loop playsinline
 *          preload="none" poster="static-poster.jpg"></video>
 */
import lottie from 'lottie-web/build/player/lottie_light';

const reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

/**
 * Is this a connection we should treat as "slow/metered" — same bar as the
 * gate `canRun3D()` uses in product-3d.js, kept consistent site-wide.
 */
function isSlowConnection() {
	const conn = navigator.connection;
	if ( ! conn ) {
		return false;
	}
	if ( conn.saveData ) {
		return true;
	}
	return /2g|3g/.test( conn.effectiveType || '' );
}

function initLottie( el ) {
	const src = el.getAttribute( 'data-lottie' );
	if ( ! src ) {
		return;
	}
	const mount = document.createElement( 'div' );
	mount.className = 'blackroll-lottie__canvas';
	el.appendChild( mount );
	try {
		lottie.loadAnimation( {
			container: mount,
			renderer: 'svg',
			loop: el.getAttribute( 'data-loop' ) === 'true',
			autoplay: true,
			path: src,
		} );
		const fb = el.querySelector( '.blackroll-lottie__fallback' );
		if ( fb ) {
			fb.style.display = 'none';
		}
	} catch ( e ) {
		mount.remove(); // Leave the static fallback image in place on any failure.
	}
}

function bootLottie() {
	const nodes = document.querySelectorAll( '.blackroll-lottie[data-lottie]' );
	if ( ! nodes.length || reduce ) {
		return; // Reduced motion or nothing to animate → static frames stay.
	}
	if ( ! ( 'IntersectionObserver' in window ) ) {
		nodes.forEach( initLottie );
		return;
	}
	const io = new IntersectionObserver(
		( entries, obs ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					initLottie( entry.target );
					obs.unobserve( entry.target );
				}
			} );
		},
		{ rootMargin: '150px 0px' }
	);
	nodes.forEach( ( n ) => io.observe( n ) );
}

/**
 * Hero background video: only assigns `src` (triggering a real network
 * request) when motion is allowed and the connection isn't slow/metered.
 * Otherwise the element is left exactly as rendered — its `poster` image is
 * what the visitor sees, with no video bytes ever requested.
 */
function bootHeroVideo() {
	const video = document.querySelector( '[data-blackroll-hero-video]' );
	if ( ! video ) {
		return;
	}
	const src = video.getAttribute( 'data-src' );
	if ( ! src || reduce || isSlowConnection() ) {
		return; // Poster stays; no motion, no bytes fetched.
	}
	video.setAttribute( 'src', src );
	video.load();
	const play = video.play();
	if ( play && typeof play.catch === 'function' ) {
		play.catch( () => {
			/* Autoplay blocked by the browser — poster stays visible, not an error. */
		} );
	}
}

function boot() {
	bootLottie();
	bootHeroVideo();
}

if ( document.readyState !== 'loading' ) {
	boot();
} else {
	document.addEventListener( 'DOMContentLoaded', boot );
}
