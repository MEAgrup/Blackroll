<?php
/**
 * Sebari lead-form helpers (Module 9, PATCH-04, PATCH-08).
 *
 * The real Blackroll embed (endpoint /add-participant/{formId}, group_contact_id,
 * user_id, final fields) is an OPEN DEPENDENCY — replace the placeholders below
 * when Sebari sends the code. The submit UX is driven by BLACKROLL_SEBARI_SUBMIT_MODE:
 *  - redirect (D1, Mode R): a hidden redirect_url points to the thank-you page.
 *    ⚠️ Confirm Sebari supports the redirect param name before go-live (Open Dep #7).
 *  - fetch (Mode F contingency): contact.js intercepts submit, POSTs via fetch,
 *    swaps in an inline success state. Needs CSP connect-src (handled in security.php).
 *
 * Honeypot is client-enforced (PATCH-08): Sebari's server does not know about it.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Real Blackroll Sebari config (delivered by client 2026-07-13).
 * Endpoint /add-participant/971, routing group 5260 / user 139954, fields
 * name + phone_number + email. Override via the filter if Sebari reissues it.
 *
 * @return array
 */
function blackroll_sebari_config() {
	return apply_filters(
		'blackroll_sebari_config',
		array(
			'form_id'          => '971',
			'group_contact_id' => '5260',
			'user_id'          => '139954',
			'title'            => __( 'Konsultasi gratis dengan team Blackroll', 'blackroll-core' ),
			// ⚠️ Open Dependency #7: the delivered embed is a plain POST with NO
			// redirect param. Mode R injects this hidden field as its best effort;
			// confirm Sebari honours it before go-live, else flip to Mode F
			// (BLACKROLL_SEBARI_SUBMIT_MODE = fetch).
			'redirect_param'   => 'redirect_url',
		)
	);
}

/**
 * Thank-you page URL per language (Mode R target).
 *
 * @return string
 */
function blackroll_thankyou_url() {
	$path = blackroll_is_en() ? '/en/contact/thank-you/' : '/kontak/terima-kasih/';
	return home_url( $path );
}

/**
 * Render the Sebari lead form (placeholder markup mirroring the sample shape).
 * Re-skinned to brand via app.css; hidden routing fields preserved verbatim.
 * Shortcode: [blackroll_sebari_form]
 */
add_shortcode(
	'blackroll_sebari_form',
	function () {
		$cfg    = blackroll_sebari_config();
		$mode   = blackroll_sebari_submit_mode();
		$action = 'https://sebari.co.id/api/group-contact-form/add-participant/' . rawurlencode( $cfg['form_id'] );

		ob_start();
		?>
		<?php if ( ! empty( $cfg['title'] ) ) : ?>
			<h2 class="blackroll-sebari-form__title"><?php echo esc_html( $cfg['title'] ); ?></h2>
		<?php endif; ?>
		<form class="blackroll-sebari-form" method="POST" action="<?php echo esc_url( $action ); ?>"
			data-submit-mode="<?php echo esc_attr( $mode ); ?>"
			data-fallback-wa="<?php echo esc_attr( blackroll_wa_link() ); ?>">

			<input type="hidden" name="group_contact_id" value="<?php echo esc_attr( $cfg['group_contact_id'] ); ?>">
			<input type="hidden" name="user_id" value="<?php echo esc_attr( $cfg['user_id'] ); ?>">
			<?php
			/*
			 * Success/failure routing is configured IN the Sebari dashboard (the
			 * delivered embed exposes no redirect param). Paste these page URLs
			 * into the Sebari form's success/failure redirect settings:
			 *   Success: /kontak/terima-kasih/
			 *   Failure: /kontak/gagal/
			 * Mode F (fetch) remains available as a fallback if Sebari can't redirect.
			 */
			?>

			<p class="blackroll-field">
				<label for="blackroll-name"><?php esc_html_e( 'Nama', 'blackroll-core' ); ?> *</label>
				<input id="blackroll-name" name="name" type="text" placeholder="<?php esc_attr_e( 'Masukkan Nama', 'blackroll-core' ); ?>" required autocomplete="name">
			</p>
			<p class="blackroll-field">
				<label for="blackroll-phone"><?php esc_html_e( 'Nomor WhatsApp', 'blackroll-core' ); ?> *</label>
				<input id="blackroll-phone" name="phone_number" type="tel" pattern="[0-9]{10,16}" placeholder="<?php esc_attr_e( 'Masukkan Nomor Whatsapp', 'blackroll-core' ); ?>" required autocomplete="tel" inputmode="numeric">
			</p>
			<p class="blackroll-field">
				<label for="blackroll-email"><?php esc_html_e( 'Email', 'blackroll-core' ); ?> *</label>
				<input id="blackroll-email" name="email" type="email" placeholder="<?php esc_attr_e( 'Masukkan Email', 'blackroll-core' ); ?>" required autocomplete="email">
			</p>

			<?php // Honeypot — visually hidden, client-enforced (PATCH-08). ?>
			<div class="blackroll-hp" aria-hidden="true">
				<label for="blackroll-website"><?php esc_html_e( 'Leave this field empty', 'blackroll-core' ); ?></label>
				<input id="blackroll-website" name="website" type="text" tabindex="-1" autocomplete="off">
			</div>

			<p class="blackroll-field">
				<button type="submit" class="wp-element-button"><?php esc_html_e( 'Kirim', 'blackroll-core' ); ?></button>
			</p>

			<p class="blackroll-sebari-form__note">
				<?php esc_html_e( 'Jika form tidak berfungsi, hubungi kami langsung via WhatsApp di bawah.', 'blackroll-core' ); ?>
			</p>
		</form>
		<?php
		return ob_get_clean();
	}
);

/**
 * Honeypot CSS (position off-screen). Small enough to inline in <head>.
 */
add_action(
	'wp_head',
	function () {
		if ( ! blackroll_is_page_role( 'contact' ) ) {
			return;
		}
		echo '<style>.blackroll-hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}</style>' . "\n";
	}
);
