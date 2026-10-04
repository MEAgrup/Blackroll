/*
 * Product-page 3D showcase (Module 2, PATCH-17; activated 2026-09 per the
 * client's premium-over-score decision, see deploy/QA-CHECKLIST.md).
 *
 * Since Fase 2 this mounts the same blind scene as the homepage hero
 * (blind-scene.js): a roller blind with the real fabric photos, which the
 * visitor can roll up/down by dragging the slider under it. Three.js is only
 * fetched after canRun3D() passes (three-gate.js); otherwise the static
 * fallback inside the container stays.
 *
 * Markup contract:
 *   <div class="blackroll-3d-slot" data-blackroll-3d
 *        data-texture="…-swatch.webp" data-chain="true|false">
 *     <… class="blackroll-3d-slot__fallback">…</…>
 *   </div>
 */
import { canRun3D, whenIdle } from './three-gate.js';

async function mount( el ) {
	let scene;
	try {
		const { mountBlindScene } = await import( './blind-scene.js' );
		scene = await mountBlindScene( el, {
			texture: el.getAttribute( 'data-texture' ),
			chain: 'true' === el.getAttribute( 'data-chain' ),
			intro: true,
		} );
	} catch ( e ) {
		return; // Fetch/WebGL failure — fallback stays, no thrown error.
	}
	el.classList.add( 'is-3d' );
	const range = el.parentElement && el.parentElement.querySelector( '[data-blackroll-3d-range]' );
	const input = range && range.querySelector( 'input' );
	if ( input ) {
		range.hidden = false;
		input.addEventListener( 'input', () => scene.setProgress( input.value / 100 ) );
	}
}

if ( canRun3D() ) {
	whenIdle( () => document.querySelectorAll( '[data-blackroll-3d][data-texture]' ).forEach( mount ) );
}
