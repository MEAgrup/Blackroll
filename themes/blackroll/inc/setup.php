<?php
/**
 * Theme setup: supports, image sizes, editor styles.
 *
 * @package Blackroll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'align-wide' );

		// Editor CSS mirrors the front-end component styles.
		add_editor_style( array( 'assets/css/app.css' ) );

		load_theme_textdomain( 'blackroll', BLACKROLL_DIR . '/languages' );

		// Image sizes tuned for the portfolio grid and swatch/preview needs.
		add_image_size( 'blackroll-card', 640, 480, true );      // Portfolio / product cards.
		add_image_size( 'blackroll-preview', 900, 900, false );  // Selector preview area.
		add_image_size( 'blackroll-swatch', 96, 96, true );      // Tiny colour swatches.
		add_image_size( 'blackroll-og', 1200, 630, true );       // Social share default.
	}
);

/**
 * Expose custom image sizes to the block editor size dropdown.
 */
add_filter(
	'image_size_names_choose',
	function ( $sizes ) {
		return array_merge(
			$sizes,
			array(
				'blackroll-card'    => __( 'Blackroll Card', 'blackroll' ),
				'blackroll-preview' => __( 'Blackroll Preview', 'blackroll' ),
				'blackroll-swatch'  => __( 'Blackroll Swatch', 'blackroll' ),
			)
		);
	}
);
