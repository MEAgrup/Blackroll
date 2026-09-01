<?php
/**
 * Config accessors and site options (single source of truth).
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option defaults. The primary WA number is the single source of truth
 * consumed by footer, Contact page, thank-you page and schema (PATCH-05, D4).
 *
 * @return array<string,string>
 */
function blackroll_option_defaults() {
	return array(
		'blackroll_primary_wa'   => '+62813-3838-8500', // D4.
		'blackroll_secondary_wa' => '',                 // Store 0813-1321-8686 — off by default.
		'blackroll_ga4_id'       => '',                 // Only used when analytics mode = ga4.
		'blackroll_maps_url'     => 'https://maps.google.com/?q=Jl.+Gatot+Subroto+No.107+Bandung',
	);
}

/**
 * Read a Blackroll option with default fallback.
 *
 * @param string $key Option key.
 * @return string
 */
function blackroll_get_option( $key ) {
	$defaults = blackroll_option_defaults();
	$value    = get_option( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
	return is_string( $value ) ? $value : '';
}

/**
 * Register settings so they appear in the options table and REST/admin UI.
 */
add_action(
	'admin_init',
	function () {
		foreach ( blackroll_option_defaults() as $key => $default ) {
			register_setting(
				'blackroll_settings',
				$key,
				array(
					'type'              => 'string',
					'default'          => $default,
					'sanitize_callback' => 'sanitize_text_field',
					'show_in_rest'      => false,
				)
			);
		}
	}
);

/**
 * Primary WA number, display form (as configured, e.g. "0813-3838-8500").
 *
 * @return string
 */
function blackroll_wa_display() {
	$raw = blackroll_get_option( 'blackroll_primary_wa' );
	// Strip +62 country prefix for a local display style.
	$local = preg_replace( '/^\+?62/', '0', preg_replace( '/[^\d+]/', '', $raw ) );
	// Re-hyphenate loosely for readability (0813-3838-8500).
	return $local ? $local : $raw;
}

/**
 * Primary WA as a wa.me deep link (digits only, 62 country code).
 *
 * @return string
 */
function blackroll_wa_link() {
	$digits = preg_replace( '/\D/', '', blackroll_get_option( 'blackroll_primary_wa' ) );
	$digits = preg_replace( '/^0/', '62', $digits );
	return 'https://wa.me/' . $digits;
}

/**
 * Current analytics mode (ga4|none).
 *
 * @return string
 */
function blackroll_analytics_mode() {
	return apply_filters( 'blackroll_analytics_mode', BLACKROLL_ANALYTICS_MODE );
}

/**
 * Current Sebari submit mode (redirect|fetch).
 *
 * @return string
 */
function blackroll_sebari_submit_mode() {
	return apply_filters( 'blackroll_sebari_submit_mode', BLACKROLL_SEBARI_SUBMIT_MODE );
}

/**
 * Homepage hero background video (client decision 2026-09: premium look over
 * loading-speed score — see deploy/QA-CHECKLIST.md). Single swappable config
 * point: empty by default so the hero stays typography-only (current, safe
 * behaviour) until the rollerblind team's asset lands — set the constant or
 * hook the filter once a real file exists, nothing else needs to change.
 *
 * @return string Absolute URL, or '' to render no video element at all.
 */
function blackroll_hero_video_url() {
	$default = defined( 'BLACKROLL_HERO_VIDEO_URL' ) ? BLACKROLL_HERO_VIDEO_URL : '';
	return apply_filters( 'blackroll_hero_video_url', $default );
}

/**
 * Poster image shown before the hero video plays, and to every visitor who
 * gets the video gated off (prefers-reduced-motion, slow/metered connection,
 * or autoplay blocked by the browser) — see assets/js/motion.js bootHeroVideo().
 *
 * @return string Absolute URL.
 */
function blackroll_hero_video_poster() {
	$default = defined( 'BLACKROLL_HERO_VIDEO_POSTER' )
		? BLACKROLL_HERO_VIDEO_POSTER
		: get_template_directory_uri() . '/assets/images/rooms/ruang-tamu.webp';
	return apply_filters( 'blackroll_hero_video_poster', $default );
}

/**
 * Mark the post-submit pages noindex,follow (PATCH-04 / PATCH-09). Runs early
 * so it also works when Rank Math is absent; Rank Math will additionally honor
 * its own robots meta on these pages.
 */
add_action(
	'wp_head',
	function () {
		if ( blackroll_is_page_role( 'thankyou' ) || blackroll_is_page_role( 'failed' ) ) {
			echo '<meta name="robots" content="noindex,follow">' . "\n";
		}
	},
	0
);

/**
 * Resolve a page "role" so the theme can load per-page scripts and mark
 * noindex pages without hardcoding IDs. Roles map to known slugs (ID + EN).
 *
 * @param string $role One of: material-color, portfolio, contact, thankyou, privacy.
 * @return bool
 */
function blackroll_is_page_role( $role ) {
	if ( ! is_page() ) {
		return false;
	}
	$slugs = array(
		'material-color' => array( 'material-warna', 'material-color' ),
		'portfolio'      => array( 'portofolio', 'portfolio' ),
		'contact'        => array( 'kontak', 'contact' ),
		'thankyou'       => array( 'terima-kasih', 'thank-you' ),
		'failed'         => array( 'gagal', 'failed' ),
		'privacy'        => array( 'kebijakan-privasi', 'privacy-policy' ),
	);
	if ( empty( $slugs[ $role ] ) ) {
		return false;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return false;
	}
	return in_array( $post->post_name, $slugs[ $role ], true );
}
