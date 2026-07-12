<?php
/**
 * Performance & hygiene: trim WP head, drop emoji, keep the front end lean.
 * SEO sitemap/robots and security headers are handled by blackroll-core.
 *
 * @package Blackroll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		// Remove the emoji detection script/style (extra request, no value here).
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );

		// Remove generator + shortlink + wlwmanifest noise.
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );

		// Adjacent post rel links are unused on a company-profile site.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
	}
);

/**
 * Drop the default WP block-library inline SVG duotone filter dump and the
 * classic-theme global-styles noise we do not use. Keeps <head> lighter.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		// Comment-reply script is irrelevant (comments are disabled site-wide).
		wp_dequeue_script( 'comment-reply' );
	},
	100
);

/**
 * Add width/height-friendly lazy loading defaults. WP already lazy-loads
 * below-the-fold images; we make sure the first in-view image is eager
 * (handled per-template) and everything else stays lazy + async decode.
 */
add_filter(
	'wp_img_tag_add_loading_attr',
	function ( $value, $image, $context ) {
		unset( $image, $context );
		return $value;
	},
	10,
	3
);
