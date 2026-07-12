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
 * Placeholder Sebari config. Replace verbatim with the Blackroll values.
 *
 * @return array
 */
function blackroll_sebari_config() {
	return apply_filters(
		'blackroll_sebari_config',
		array(
			// PLACEHOLDER — confirm with Sebari team (Open Dependency #1).
			'form_id'          => 'FORM_ID', // e.g. 952
			'group_contact_id' => 'GROUP_CONTACT_ID',
			'user_id'          => 'USER_ID',
			'redirect_param'   => 'redirect_url', // ⚠️ confirm exact param name (Open Dep #7).
		)
	);
}

/**
 * Thank-you page URL per language (Mode R target).
 *
 * @return string
 */
function blackroll_thankyou_url() {
	$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'id';
	$path = ( 'en' === $lang ) ? '/en/contact/thank-you/' : '/kontak/terima-kasih/';
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
		<!-- SEBARI_EMBED: replace endpoint + routing IDs with the Blackroll form code -->
		<form class="blackroll-sebari-form" method="POST" action="<?php echo esc_url( $action ); ?>"
			data-submit-mode="<?php echo esc_attr( $mode ); ?>"
			data-fallback-wa="<?php echo esc_attr( blackroll_wa_link() ); ?>">

			<input type="hidden" name="group_contact_id" value="<?php echo esc_attr( $cfg['group_contact_id'] ); ?>">
			<input type="hidden" name="user_id" value="<?php echo esc_attr( $cfg['user_id'] ); ?>">
			<?php if ( 'redirect' === $mode ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $cfg['redirect_param'] ); ?>" value="<?php echo esc_url( blackroll_thankyou_url() ); ?>">
			<?php endif; ?>

			<p class="blackroll-field">
				<label for="blackroll-name"><?php esc_html_e( 'Nama', 'blackroll-core' ); ?> *</label>
				<input id="blackroll-name" name="name" type="text" required autocomplete="name">
			</p>
			<p class="blackroll-field">
				<label for="blackroll-phone"><?php esc_html_e( 'Nomor WhatsApp', 'blackroll-core' ); ?> *</label>
				<input id="blackroll-phone" name="phone_number" type="tel" pattern="[0-9]{10,16}" required autocomplete="tel" inputmode="numeric">
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
