<?php
/**
 * Asset loading: self-hosted fonts (preloaded), component CSS, deferred JS.
 *
 * Performance rules (Module 10): no render-blocking JS in <head>, fonts
 * self-hosted with font-display:swap and preloaded, critical CSS small.
 *
 * @package Blackroll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preload the two critical WOFF2 files (Anton latin + Inter variable latin)
 * so the LCP heading text does not wait on a late font request.
 */
add_action(
	'wp_head',
	function () {
		$fonts = array(
			'assets/fonts/anton-latin-400.woff2',
			'assets/fonts/inter-latin-var.woff2',
		);
		foreach ( $fonts as $rel ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( BLACKROLL_URI . '/' . $rel )
			);
		}

		// Favicon (placeholder mark — swap for the final brand asset).
		printf(
			'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
			esc_url( BLACKROLL_URI . '/assets/images/favicon.svg' )
		);
	},
	1
);

add_action(
	'wp_enqueue_scripts',
	function () {
		// @font-face declarations (references the self-hosted files above).
		wp_enqueue_style(
			'blackroll-fonts',
			BLACKROLL_URI . '/assets/css/fonts.css',
			array(),
			BLACKROLL_VERSION
		);

		// Component / layout styles (design tokens come from theme.json).
		wp_enqueue_style(
			'blackroll-app',
			BLACKROLL_URI . '/assets/css/app.css',
			array( 'blackroll-fonts' ),
			BLACKROLL_VERSION
		);

		// Motion loader: lazy Lottie via IntersectionObserver, reduced-motion aware.
		// Deferred so it never blocks paint. It self-limits on prefers-reduced-motion.
		wp_enqueue_script(
			'blackroll-motion',
			BLACKROLL_URI . '/assets/js/motion.js',
			array(),
			BLACKROLL_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
);

/**
 * Ship the front-end selector/portfolio/contact scripts only on the pages
 * that use them. Templates set a body class or we detect via the plugin's
 * page-role helper. Kept tiny and deferred.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$handles = array();

		if ( function_exists( 'blackroll_is_page_role' ) ) {
			if ( blackroll_is_page_role( 'material-color' ) ) {
				$handles['blackroll-selector'] = 'assets/js/selector.js';
			}
			if ( blackroll_is_page_role( 'portfolio' ) || is_post_type_archive( 'project' ) ) {
				$handles['blackroll-portfolio'] = 'assets/js/portfolio.js';
			}
			if ( blackroll_is_page_role( 'contact' ) ) {
				$handles['blackroll-contact'] = 'assets/js/contact.js';
			}
		}

		foreach ( $handles as $handle => $rel ) {
			wp_enqueue_script(
				$handle,
				BLACKROLL_URI . '/' . $rel,
				array(),
				BLACKROLL_VERSION,
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
);
