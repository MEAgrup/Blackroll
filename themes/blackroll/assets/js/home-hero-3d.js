/*
 * Homepage hero — "The Blind Reveal" (Fase 2, docs/PLAN-REVISI-PREMIUM.md).
 *
 * Progressive enhancement over a complete static hero:
 *   1. Always: the colour chips swap the fallback photo (works without WebGL).
 *   2. If canRun3D(): after idle, load blind-scene.js (+ three.js) and replace
 *      the photo with the 3D blind. It drops down on load, rolls up as the
 *      hero scrolls away, the chips re-texture the fabric, and the
 *      Roller/Zebra toggle switches the system.
 * The H1/CTA stay plain HTML outside the canvas (SEO + LCP).
 *
 * Markup contract (patterns/home-hero.php):
 *   [data-blackroll-hero]
 *     [data-blackroll-hero-stage]  img.blackroll-hero__fallback
 *     button[data-hero-swatch][data-texture][data-front][data-mode]
 *     button[data-hero-mode="roller|zebra"]
 */
import { canRun3D, whenIdle } from './three-gate.js';

const hero = document.querySelector( '[data-blackroll-hero]' );

if ( hero ) {
	const stage = hero.querySelector( '[data-blackroll-hero-stage]' );
	const fallback = stage && stage.querySelector( '.blackroll-hero__fallback' );
	const chips = Array.from( hero.querySelectorAll( '[data-hero-swatch]' ) );
	const modes = Array.from( hero.querySelectorAll( '[data-hero-mode]' ) );
	const label = hero.querySelector( '[data-hero-label]' );
	let scene = null;
	let mode = 'roller';

	function press( list, btn ) {
		list.forEach( ( b ) => b.setAttribute( 'aria-pressed', b === btn ? 'true' : 'false' ) );
	}

	function visibleChips() {
		return chips.filter( ( c ) => ( c.getAttribute( 'data-mode' ) || 'roller' ) === mode );
	}

	function choose( chip ) {
		press( chips, chip );
		if ( label ) {
			label.textContent = chip.getAttribute( 'data-label' ) || '';
		}
		if ( scene ) {
			scene.setTexture( chip.getAttribute( 'data-texture' ) );
		} else if ( fallback && chip.getAttribute( 'data-front' ) ) {
			fallback.src = chip.getAttribute( 'data-front' );
		}
	}

	function setMode( m ) {
		mode = m;
		press( modes, modes.find( ( b ) => b.getAttribute( 'data-hero-mode' ) === m ) );
		chips.forEach( ( c ) => {
			c.hidden = ( c.getAttribute( 'data-mode' ) || 'roller' ) !== m;
		} );
		if ( scene ) {
			scene.setMode( m );
			onScroll();
		}
		const first = visibleChips()[ 0 ];
		if ( first ) {
			choose( first );
		}
	}

	chips.forEach( ( c ) => c.addEventListener( 'click', () => choose( c ) ) );
	modes.forEach( ( b ) => b.addEventListener( 'click', () => setMode( b.getAttribute( 'data-hero-mode' ) ) ) );

	function onScroll() {
		if ( ! scene ) {
			return;
		}
		const r = hero.getBoundingClientRect();
		const p = Math.min( 1, Math.max( 0, -r.top / ( r.height * 0.75 ) ) );
		scene.setProgress( 'zebra' === mode ? 1 - p : p * 0.92 );
	}

	if ( stage && canRun3D() ) {
		whenIdle( async () => {
			const active = chips.find( ( c ) => 'true' === c.getAttribute( 'aria-pressed' ) ) || chips[ 0 ];
			try {
				const { mountBlindScene } = await import( './blind-scene.js' );
				scene = await mountBlindScene( stage, {
					texture: active.getAttribute( 'data-texture' ),
					mode,
					chain: true,
					intro: true,
				} );
			} catch ( e ) {
				return; // Keep the photo.
			}
			stage.classList.add( 'is-3d' );
			window.addEventListener( 'scroll', onScroll, { passive: true } );
			onScroll();
		} );
	}
}
