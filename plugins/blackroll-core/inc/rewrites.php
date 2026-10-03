<?php
/**
 * Localized archive-base rewrites (PATCH-10, option a) + sitemap hygiene.
 *
 * Polylang free translates page slugs but not CPT/archive rewrite bases.
 * We add the EN base for the project archive so /en/portfolio/ resolves to the
 * same archive as /portofolio/. ~30 lines, zero cost, keeps localized slugs.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the localized rewrite rules.
 */
function blackroll_register_rewrites() {
	// EN portfolio archive base → project post-type archive.
	add_rewrite_rule( '^en/portfolio/?$', 'index.php?post_type=project&lang=en', 'top' );
	add_rewrite_rule( '^en/portfolio/page/([0-9]{1,})/?$', 'index.php?post_type=project&lang=en&paged=$matches[1]', 'top' );
}
add_action( 'init', 'blackroll_register_rewrites', 20 );

/**
 * Disable WP core sitemap — Rank Math generates its own per-language sitemap
 * (PATCH-10; prevents duplicate sitemap submission to GSC).
 */
add_filter( 'wp_sitemaps_enabled', '__return_false' );

/**
 * Disable comments site-wide (Module 8 / PATCH-15). Company-profile site.
 */
add_action(
	'init',
	function () {
		// Close comments/pings on the front end.
		add_filter( 'comments_open', '__return_false', 20 );
		add_filter( 'pings_open', '__return_false', 20 );
		add_filter( 'comments_array', '__return_empty_array', 10 );
	}
);
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
	}
);
add_action(
	'init',
	function () {
		foreach ( get_post_types() as $type ) {
			if ( post_type_supports( $type, 'comments' ) ) {
				remove_post_type_support( $type, 'comments' );
				remove_post_type_support( $type, 'trackbacks' );
			}
		}
	},
	100
);
