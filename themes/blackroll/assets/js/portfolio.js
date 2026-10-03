/*
 * Portfolio filter + Load More + accessible lightbox (Module 7).
 * - Filter: class toggling, aria-pressed on the active filter.
 * - Lightbox: focus trap, Esc closes, focus returns to the trigger.
 * CSS transitions only (no Lottie here). Refined against the project CPT in Step 8.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-blackroll-portfolio]' );
	if ( ! root ) {
		return;
	}

	/* ---- Filter ---- */
	var filterBar = root.querySelector( '.blackroll-filter' );
	if ( filterBar ) {
		filterBar.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.blackroll-filter__btn' );
			if ( ! btn ) {
				return;
			}
			var type = btn.getAttribute( 'data-filter' );
			filterBar.querySelectorAll( '.blackroll-filter__btn' ).forEach( function ( b ) {
				b.setAttribute( 'aria-pressed', b === btn ? 'true' : 'false' );
			} );
			root.querySelectorAll( '.blackroll-grid__item' ).forEach( function ( item ) {
				var t = item.getAttribute( 'data-type' );
				item.hidden = ! ( type === 'all' || t === type );
			} );
		} );
	}

	/* ---- Load More ---- */
	var more = root.querySelector( '[data-load-more]' );
	if ( more ) {
		more.addEventListener( 'click', function () {
			root.querySelectorAll( '.blackroll-grid__item[data-deferred]' ).forEach( function ( item ) {
				item.removeAttribute( 'data-deferred' );
				item.hidden = false;
			} );
			more.remove();
		} );
	}

	/* ---- Lightbox ---- */
	var lb = document.querySelector( '.blackroll-lightbox' );
	if ( ! lb ) {
		return;
	}
	var lbImg = lb.querySelector( '.blackroll-lightbox__img' );
	var lbClose = lb.querySelector( '.blackroll-lightbox__close' );
	var lastTrigger = null;

	function openLightbox( src, alt, trigger ) {
		lastTrigger = trigger;
		lbImg.src = src;
		lbImg.alt = alt || '';
		lb.classList.add( 'is-open' );
		lbClose.focus();
		document.addEventListener( 'keydown', onKey );
	}
	function closeLightbox() {
		lb.classList.remove( 'is-open' );
		document.removeEventListener( 'keydown', onKey );
		if ( lastTrigger ) {
			lastTrigger.focus();
		}
	}
	function onKey( e ) {
		if ( e.key === 'Escape' ) {
			closeLightbox();
		}
		if ( e.key === 'Tab' ) {
			// Trap: only the close button is focusable inside.
			e.preventDefault();
			lbClose.focus();
		}
	}

	root.addEventListener( 'click', function ( e ) {
		var trigger = e.target.closest( '[data-lightbox]' );
		if ( ! trigger ) {
			return;
		}
		e.preventDefault();
		openLightbox( trigger.getAttribute( 'data-lightbox' ), trigger.getAttribute( 'data-alt' ), trigger );
	} );
	lbClose.addEventListener( 'click', closeLightbox );
	lb.addEventListener( 'click', function ( e ) {
		if ( e.target === lb ) {
			closeLightbox();
		}
	} );
}() );
