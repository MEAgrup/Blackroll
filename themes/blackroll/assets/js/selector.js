/*
 * Material & Color selector (Module 6).
 * Vanilla-JS state: material × series → shade × size. Filters the shade
 * swatches by the selected Series + Material (Module 6 Rule: shades render
 * dynamically per Series), swaps the preview via the fallback chain
 * (combination → shade → series → generic) and updates the label.
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
		swatchWrap.querySelectorAll( '.blackroll-swatch' ).forEach( function ( sw ) {
			var okSeries = ! state.series || sw.getAttribute( 'data-series' ) === state.series;
			var mats = ( sw.getAttribute( 'data-materials' ) || '' ).split( /[\s,]+/ );
			var okMat = ! state.material || mats.indexOf( state.material ) !== -1;
			var show = okSeries && okMat;
			sw.hidden = ! show;
			if ( show && ! firstVisible ) {
				firstVisible = sw;
			}
		} );
		return firstVisible;
	}

	function previewFor( btn ) {
		if ( ! btn ) {
			return root.getAttribute( 'data-preview-generic' ) || '';
		}
		return (
			btn.getAttribute( 'data-preview-' + ( state.material || '' ) ) ||
			btn.getAttribute( 'data-preview' ) ||
			root.getAttribute( 'data-preview-' + ( state.series || '' ) ) ||
			root.getAttribute( 'data-preview-generic' ) ||
			''
		);
	}

	function swapPreview( src ) {
		if ( ! preview || ! src || preview.getAttribute( 'src' ) === src ) {
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
		if ( state.sku ) {
			parts.push( 'SKU ' + state.sku );
		}
		label.textContent = parts.join( ' · ' );
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
			filterShades();
			if ( current && current.hidden ) {
				current.setAttribute( 'aria-pressed', 'false' );
				state.shade = null;
				state.sku = null;
				swapPreview( previewFor( null ) );
			}
		}
		updateLabel();
	} );

	filterShades();
}() );
