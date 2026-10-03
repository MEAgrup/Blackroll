/*
 * Contact form behaviour (Module 9, PATCH-04/08).
 * - Honeypot: drop submission client-side if the hidden "website" field is filled.
 * - Mode R (redirect): let the browser POST normally (Sebari redirect_url handles UX).
 * - Mode F (fetch): intercept, fetch() POST, swap in an inline success/error state.
 */
( function () {
	'use strict';

	var form = document.querySelector( '.blackroll-sebari-form' );
	if ( ! form ) {
		return;
	}
	var mode = form.getAttribute( 'data-submit-mode' ) || 'redirect';
	var waLink = form.getAttribute( 'data-fallback-wa' ) || '#';

	function honeypotTripped() {
		var hp = form.querySelector( '[name="website"]' );
		return hp && hp.value.trim() !== '';
	}

	function showMessage( html ) {
		var box = document.createElement( 'div' );
		box.className = 'blackroll-sebari-form__result';
		box.setAttribute( 'role', 'status' );
		box.innerHTML = html;
		form.replaceWith( box );
	}

	form.addEventListener( 'submit', function ( e ) {
		// Honeypot guard applies in BOTH modes.
		if ( honeypotTripped() ) {
			e.preventDefault();
			return;
		}

		if ( mode !== 'fetch' ) {
			return; // Mode R: normal POST + Sebari redirect.
		}

		// Mode F: intercept.
		e.preventDefault();
		var data = new FormData( form );
		var btn = form.querySelector( 'button[type="submit"]' );
		if ( btn ) {
			btn.disabled = true;
		}

		fetch( form.action, { method: 'POST', body: data } )
			.then( function ( res ) {
				if ( res.ok ) {
					showMessage(
						'<h3>✓ Terima kasih!</h3><p>Pesan Anda sudah kami terima. Cek WhatsApp Anda — tim kami akan menghubungi segera.</p>'
					);
				} else {
					throw new Error( 'bad status' );
				}
			} )
			.catch( function () {
				if ( btn ) {
					btn.disabled = false;
				}
				var err = form.querySelector( '.blackroll-sebari-form__error' );
				if ( ! err ) {
					err = document.createElement( 'p' );
					err.className = 'blackroll-sebari-form__error';
					err.setAttribute( 'role', 'alert' );
					form.appendChild( err );
				}
				err.innerHTML =
					'Ada masalah mengirim pesan. Coba lagi atau hubungi kami via <a href="' +
					waLink +
					'" rel="noopener">WhatsApp</a>.';
			} );
	} );
}() );
