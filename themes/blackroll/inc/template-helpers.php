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

/*
 * blackroll_is_en() lives in blackroll-core (inc/i18n.php). This copy only
 * keeps the theme rendering if the plugin is inactive.
 */
if ( ! function_exists( 'blackroll_is_en' ) ) {
	function blackroll_is_en() {
		if ( ! function_exists( 'pll_current_language' ) || 'en' !== pll_current_language() ) {
			return false;
		}
		$langs = function_exists( 'pll_languages_list' ) ? (array) pll_languages_list() : array();
		return in_array( 'id', $langs, true );
	}
}

/**
 * Language-aware Contact page URL.
 *
 * @return string
 */
function blackroll_contact_url() {
	$path = blackroll_is_en() ? '/en/contact/' : '/kontak/';
	return esc_url( home_url( $path ) );
}

/**
 * Short reassurance/CTA label per language.
 *
 * @return string
 */
function blackroll_cta_label() {
	return blackroll_is_en() ? 'Contact Us' : 'Hubungi Kami';
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

/**
 * <img> for a curated collection photo shipped with the theme
 * (assets/images/collection/, see the README there). Width/height are read
 * from the file so the browser reserves space (no layout shift).
 *
 * @param string $file    File name inside assets/images/collection/.
 * @param string $alt     Alt text.
 * @param string $loading 'lazy' (default) or 'eager'.
 * @return string HTML, or '' when the file is missing.
 */
function blackroll_collection_img( $file, $alt, $loading = 'lazy' ) {
	$path = get_theme_file_path( 'assets/images/collection/' . $file );
	if ( ! is_readable( $path ) ) {
		return '';
	}
	$size = wp_getimagesize( $path );
	return sprintf(
		'<img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" loading="%5$s" decoding="async">',
		esc_url( get_theme_file_uri( 'assets/images/collection/' . $file ) ),
		esc_attr( $alt ),
		$size ? (int) $size[0] : 1600,
		$size ? (int) $size[1] : 1600,
		esc_attr( $loading )
	);
}

/**
 * Product photo gallery: one large lead image + a row of detail shots.
 *
 * @param array<int,array{0:string,1:string}> $items [ file, alt ] pairs; the first is the lead image.
 * @return string HTML.
 */
function blackroll_product_gallery( $items ) {
	$out      = '';
	$portrait = false;
	foreach ( array_values( $items ) as $i => $item ) {
		$img = blackroll_collection_img( $item[0], $item[1] );
		if ( ! $img ) {
			continue;
		}
		if ( 0 === $i ) {
			$size     = wp_getimagesize( get_theme_file_path( 'assets/images/collection/' . $item[0] ) );
			$portrait = $size && $size[1] > $size[0];
		}
		$out .= '<figure class="blackroll-gallery__item' . ( 0 === $i ? ' blackroll-gallery__item--lead' : '' ) . '">' . $img . '</figure>';
	}
	// A portrait lead image sits in a tall left column beside the details
	// instead of being cropped to a wide banner.
	return $out ? '<div class="blackroll-gallery' . ( $portrait ? ' blackroll-gallery--portrait' : '' ) . '">' . $out . '</div>' : '';
}

/**
 * Official Blackroll logo as inline SVG (inherits the text colour), linked to
 * the homepage. Traced from the client's logo file (assets/images/brand/).
 *
 * @param string $variant 'horizontal' (header) or 'full' (stacked + "Black is Cool", footer).
 * @return string HTML.
 */
function blackroll_logo( $variant = 'horizontal' ) {
	$file = 'full' === $variant ? 'logo-full.svg' : 'logo-horizontal.svg';
	$svg  = file_get_contents( get_theme_file_path( 'assets/images/' . $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	return sprintf(
		'<a class="blackroll-logo blackroll-logo--%1$s" href="%2$s" rel="home">%3$s</a>',
		esc_attr( $variant ),
		esc_url( home_url( '/' ) ),
		$svg
	);
}
