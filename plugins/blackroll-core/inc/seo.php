<?php
/**
 * SEO layer (Module 10 / Step 11).
 *
 * Rank Math Lite is the primary SEO engine (meta, schema, hreflang, sitemap).
 * Everything here is a FALLBACK that only runs when Rank Math is absent, so a
 * fresh install (or dev) still has canonical, OG/Twitter and a robots.txt.
 * When Rank Math is active, it owns all of this and these functions no-op.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is a dedicated SEO plugin (Rank Math / Yoast) active?
 *
 * @return bool
 */
function blackroll_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' );
}

/**
 * Default social share image (monochrome logo card 1200×630).
 *
 * @return string
 */
function blackroll_default_og_image() {
	return get_template_directory_uri() . '/assets/images/og-default.jpg';
}

/**
 * Emit canonical + OG + Twitter tags when no SEO plugin is present.
 */
add_action(
	'wp_head',
	function () {
		if ( blackroll_seo_plugin_active() || is_admin() ) {
			return;
		}

		$title = wp_get_document_title();
		$url   = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
		$url   = trailingslashit( $url );

		$desc = '';
		if ( is_singular() ) {
			$desc = get_the_excerpt();
		}
		if ( ! $desc ) {
			$desc = get_bloginfo( 'description' );
		}
		$desc = wp_trim_words( wp_strip_all_tags( $desc ), 30 );

		$image = '';
		if ( is_singular() && has_post_thumbnail() ) {
			$image = get_the_post_thumbnail_url( null, 'blackroll-og' );
		}
		if ( ! $image ) {
			$image = blackroll_default_og_image();
		}

		$tags = array(
			'<link rel="canonical" href="' . esc_url( $url ) . '">',
			'<meta property="og:site_name" content="Blackroll">',
			'<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">',
			'<meta property="og:title" content="' . esc_attr( $title ) . '">',
			'<meta property="og:description" content="' . esc_attr( $desc ) . '">',
			'<meta property="og:url" content="' . esc_url( $url ) . '">',
			'<meta property="og:image" content="' . esc_url( $image ) . '">',
			'<meta name="twitter:card" content="summary_large_image">',
			'<meta name="twitter:title" content="' . esc_attr( $title ) . '">',
			'<meta name="twitter:description" content="' . esc_attr( $desc ) . '">',
			'<meta name="twitter:image" content="' . esc_url( $image ) . '">',
		);
		echo "\n" . implode( "\n", $tags ) . "\n";
	},
	6
);

/**
 * robots.txt: block everything on non-production (staging launch-killer guard,
 * PATCH-15), otherwise allow and point at the sitemap. Rank Math manages its
 * own sitemap URL; we reference the conventional path.
 */
add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( 'production' !== wp_get_environment_type() || '0' === (string) $public ) {
			return "User-agent: *\nDisallow: /\n";
		}
		$sitemap = blackroll_seo_plugin_active() ? home_url( '/sitemap_index.xml' ) : home_url( '/wp-sitemap.xml' );
		$output .= "\nSitemap: " . esc_url( $sitemap ) . "\n";
		return $output;
	},
	10,
	2
);
