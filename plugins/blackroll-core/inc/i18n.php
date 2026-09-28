<?php
/**
 * Bilingual helpers (Module 1 / Module 10 / D6).
 *
 * - [blackroll_language_switcher] renders Polylang's switcher when active.
 * - hreflang tags for ID/EN pairs are emitted by Polylang; a minimal fallback
 *   self-referential hreflang is added only when Polylang is absent.
 * Progressive EN (D6): Polylang emits hreflang only for pairs that exist, so
 * partial EN is SEO-safe.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is the current request the English (secondary) language?
 *
 * ID is primary (D6). Only treat a request as EN when Polylang actually has
 * an Indonesian language configured alongside it — an install where Polylang
 * holds just one language (as seen on production: everything tagged en-US)
 * must still render the ID labels and /kontak/, otherwise every CTA points
 * at a /en/contact/ page that does not exist (404).
 *
 * @return bool
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

add_shortcode(
	'blackroll_language_switcher',
	function () {
		if ( ! function_exists( 'pll_the_languages' ) ) {
			return ''; // Polylang not active yet — nothing to switch.
		}
		$out = pll_the_languages(
			array(
				'echo'               => 0,
				'display_names_as'   => 'slug',
				'hide_if_no_translation' => 0,
				'hide_current'       => 0,
			)
		);
		return '<ul class="blackroll-lang-switcher">' . $out . '</ul>';
	}
);

/**
 * Self-referential hreflang fallback when Polylang is inactive (so the tag
 * never disappears entirely during setup). Polylang, once active, owns this.
 */
add_action(
	'wp_head',
	function () {
		if ( function_exists( 'pll_the_languages' ) || is_admin() ) {
			return;
		}
		global $wp;
		$url = home_url( add_query_arg( array(), $wp->request ? $wp->request : '' ) );
		printf( '<link rel="alternate" hreflang="id" href="%s">' . "\n", esc_url( trailingslashit( $url ) ) );
		printf( '<link rel="alternate" hreflang="x-default" href="%s">' . "\n", esc_url( trailingslashit( $url ) ) );
	},
	5
);
