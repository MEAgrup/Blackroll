<?php
/**
 * Presentation helpers used by templates, parts and patterns.
 *
 * Data/config helpers (WA number, config flags, page roles) are defined in
 * blackroll-core. Everything here is guarded so the theme still renders if
 * the plugin is inactive.
 *
 * @package Blackroll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Language-aware Contact page URL.
 *
 * @return string
 */
function blackroll_contact_url() {
	$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'id';
	$path = ( 'en' === $lang ) ? '/en/contact/' : '/kontak/';
	return esc_url( home_url( $path ) );
}

/**
 * Short reassurance/CTA label per language.
 *
 * @return string
 */
function blackroll_cta_label() {
	$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'id';
	return ( 'en' === $lang ) ? 'Contact Us' : 'Hubungi Kami';
}

/**
 * Render the bottom-floating CTA. CSS handles base visibility (no JS needed).
 * Safe-area insets applied in app.css.
 */
function blackroll_render_floating_cta() {
	$url   = blackroll_contact_url();
	$label = blackroll_cta_label();
	printf(
		'<a class="blackroll-floating-cta" href="%1$s" aria-label="%2$s"><span class="blackroll-floating-cta__text">%2$s</span></a>',
		esc_url( $url ),
		esc_html( $label )
	);
}

/**
 * Shortcode wrapper so the floating CTA can live inside a block template part.
 */
add_shortcode(
	'blackroll_floating_cta',
	function () {
		ob_start();
		blackroll_render_floating_cta();
		return ob_get_clean();
	}
);

/**
 * Shortcode: always-visible fallback contact block (WA / IG / Shopee / TikTok).
 * Reads the primary WA number from blackroll-core when available.
 */
add_shortcode(
	'blackroll_fallback_contact',
	function () {
		$wa_link = function_exists( 'blackroll_wa_link' ) ? blackroll_wa_link() : '#';
		$wa_disp = function_exists( 'blackroll_wa_display' ) ? blackroll_wa_display() : '0813-3838-8500';

		ob_start();
		?>
		<div class="blackroll-fallback-contact">
			<p class="blackroll-fallback-contact__lead">
				<?php echo esc_html( blackroll_cta_label() ); ?>:
			</p>
			<ul class="blackroll-fallback-contact__list">
				<li><a href="<?php echo esc_url( $wa_link ); ?>" rel="noopener">WhatsApp: <?php echo esc_html( $wa_disp ); ?></a></li>
				<li><a href="https://instagram.com/blackroll.blinds" rel="noopener">Instagram: @blackroll.blinds</a></li>
				<li><a href="https://www.tiktok.com/@blackroll.official" rel="noopener">TikTok Shop: @blackroll.official</a></li>
				<li><a href="https://shopee.co.id/blackroll.official" rel="noopener">Shopee: @blackroll.official</a></li>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}
);
