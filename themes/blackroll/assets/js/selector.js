/*
 * Material & Color selector (Module 6).
 * Vanilla-JS state: material × series → shade × size. Updates preview image via
 * the fallback chain (combination → shade → series → generic) and the label.
 * Progressive enhancement — real HTML controls exist server-side; JS only swaps
 * the preview and syncs aria-pressed. No URL params (Module 6 Rule 4).
 *
 * Fully wired against the shade CPT in Step 7; this handles the interaction layer.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-blackroll-selector]' );
	if ( ! root ) {
		return;
	}

	var state = { material: null, series: null, shade: null, size: null };
	var preview = root.querySelector( '.blackroll-selector__preview img' );
	var label = root.querySelector( '.blackroll-selector__label' );

	function setPressed( group, btn ) {
		group.querySelectorAll( '[aria-pressed]' ).forEach( function ( b ) {
			b.setAttribute( 'aria-pressed', b === btn ? 'true' : 'false' );
		} );
	}

	function resolvePreview( btn ) {
		// Preview fallback chain via data attributes on the shade button.
		if ( ! btn ) {
			return null;
		}
		return (
			btn.getAttribute( 'data-preview-' + ( state.material || '' ) ) ||
			btn.getAttribute( 'data-preview' ) ||
			root.getAttribute( 'data-preview-' + ( state.series || '' ) ) ||
			root.getAttribute( 'data-preview-generic' )
		);
	}

	function updateLabel() {
		if ( ! label ) {
			return;
		}
		var parts = [ state.material, state.series, state.shade, state.size ].filter( Boolean );
		label.textContent = parts.join( ' · ' );
	}

	root.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-select]' );
		if ( ! btn ) {
			return;
		}
		var group = btn.closest( '.blackroll-option-group' );
		var kind = btn.getAttribute( 'data-select' ); // material|series|shade|size
		state[ kind ] = btn.getAttribute( 'data-value' );
		if ( group ) {
			setPressed( group, btn );
		}

		if ( 'shade' === kind && preview ) {
			var src = resolvePreview( btn );
			if ( src ) {
				preview.style.opacity = '0';
				var img = new Image();
				img.onload = function () {
					preview.src = src;
					preview.style.opacity = '1';
				};
				img.src = src;
			}
		}
		updateLabel();
	} );
}() );
