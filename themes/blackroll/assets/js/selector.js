/*
 * Material & Color selector (Module 6).
 * Vanilla-JS state: material × series → shade × size. Filters the shade
 * swatches by the selected Series + Material (Module 6 Rule: shades render
 * dynamically per Series), swaps the preview via the fallback chain
 * (combination → shade; otherwise a "photo coming soon" note) and updates the label.
 * Progressive enhancement — real HTML controls exist server-side; no URL params.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-blackroll-selector]' );
	if ( ! root ) {
		return;
	}

	var preview = root.querySelector( '.blackroll-selector__preview img' );
	var label = root.querySelector( '.blackroll-selector__label' );
	var swatchWrap = root.querySelector( '.blackroll-swatches' );
	var empty = root.querySelector( '.blackroll-selector__empty' );

	function pressed( kind ) {
		var btn = root.querySelector( '[data-select="' + kind + '"][aria-pressed="true"]' );
		return btn ? btn.getAttribute( 'data-value' ) : null;
	}

	var state = {
		material: pressed( 'material' ),
		series: pressed( 'series' ),
		shade: null,
		sku: null,
		size: null,
	};

	function setPressed( group, btn ) {
		group.querySelectorAll( '[aria-pressed]' ).forEach( function ( b ) {
			b.setAttribute( 'aria-pressed', b === btn ? 'true' : 'false' );
		} );
	}

	// Show only shades matching the current series + material.
	function filterShades() {
		if ( ! swatchWrap ) {
			return;
		}
		var firstVisible = null;
		var firstWithPhoto = null;
		swatchWrap.querySelectorAll( '.blackroll-swatch' ).forEach( function ( sw ) {
			var okSeries = ! state.series || sw.getAttribute( 'data-series' ) === state.series;
			var mats = ( sw.getAttribute( 'data-materials' ) || '' ).split( /[\s,]+/ );
			var okMat = ! state.material || mats.indexOf( state.material ) !== -1;
			var show = okSeries && okMat;
			sw.hidden = ! show;
			if ( show && ! firstVisible ) {
				firstVisible = sw;
			}
			if ( show && ! firstWithPhoto && previewFor( sw ) ) {
				firstWithPhoto = sw;
			}
		} );
		// Prefer a shade that actually has a photo for the default selection.
		return firstWithPhoto || firstVisible;
	}

	function previewFor( btn ) {
		if ( ! btn ) {
			return root.getAttribute( 'data-preview-generic' ) || '';
		}
		// Only this shade's own photos: falling back to another shade's photo
		// showed the wrong colour for the selected SKU.
		return (
			btn.getAttribute( 'data-preview-' + ( state.material || '' ) ) ||
			btn.getAttribute( 'data-preview' ) ||
			''
		);
	}

	// No photo for this combination yet: hide the image and show the note
	// instead of an empty dark box.
	function showEmpty( isEmpty ) {
		if ( empty ) {
			empty.hidden = ! isEmpty;
		}
		if ( preview && isEmpty ) {
			preview.hidden = true;
		}
	}

	function swapPreview( src ) {
		if ( ! preview ) {
			return;
		}
		if ( ! src ) {
			showEmpty( true );
			return;
		}
		showEmpty( false );
		if ( preview.getAttribute( 'src' ) === src ) {
			preview.removeAttribute( 'hidden' );
			return;
		}
		preview.style.opacity = '0';
		var img = new Image();
		img.onload = function () {
			preview.src = src;
			// Server-rendered with [hidden] when no shade had an image yet.
			preview.removeAttribute( 'hidden' );
			preview.style.opacity = '1';
		};
		img.src = src;
	}

	function updateLabel() {
		if ( ! label ) {
			return;
		}
		var parts = [];
		if ( state.material ) {
			parts.push( state.material.replace( '_', ' ' ) );
		}
		if ( state.shade ) {
			parts.push( state.shade );
		}
		if ( state.size ) {
			parts.push( state.size + ' cm' );
		}
		if ( state.sku && state.sku !== state.shade ) {
			parts.push( 'SKU ' + state.sku );
		}
		label.textContent = parts.join( ' · ' );
	}

	// Select a shade (used on load and when the filters hide the current one).
	function selectShade( btn ) {
		if ( ! swatchWrap ) {
			return;
		}
		setPressed( swatchWrap, btn );
		state.shade = btn ? btn.getAttribute( 'data-value' ) : null;
		state.sku = btn ? btn.getAttribute( 'data-sku' ) : null;
		swapPreview( btn ? previewFor( btn ) : '' );
	}

	root.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-select]' );
		if ( ! btn ) {
			return;
		}
		var group = btn.closest( '.blackroll-option-group' );
		var kind = btn.getAttribute( 'data-select' );
		state[ kind ] = btn.getAttribute( 'data-value' );
		if ( group ) {
			setPressed( group, btn );
		}

		if ( 'shade' === kind ) {
			state.sku = btn.getAttribute( 'data-sku' );
			swapPreview( previewFor( btn ) );
		} else if ( 'material' === kind || 'series' === kind ) {
			// Re-filter shades; clear a now-hidden shade selection.
			var current = swatchWrap ? swatchWrap.querySelector( '.blackroll-swatch[aria-pressed="true"]' ) : null;
			var firstVisible = filterShades();
			if ( ! current || current.hidden ) {
				selectShade( firstVisible );
			} else {
				swapPreview( previewFor( current ) );
			}
		}
		updateLabel();
	} );

	selectShade( filterShades() );
	updateLabel();
}() );
