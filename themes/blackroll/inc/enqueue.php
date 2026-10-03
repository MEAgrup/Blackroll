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
 * Preload the two critical WOFF2 files (Fraunces latin — the H1/LCP face since
 * Fase 1 — + Inter variable latin) so the LCP heading text does not wait on a
 * late font request. Anton is now only used for stat numerals, so it is not
 * preloaded.
 */
add_action(
	'wp_head',
	function () {
		$fonts = array(
			'assets/fonts/fraunces-latin-wght-normal.woff2',
			'assets/fonts/inter-latin-var.woff2',
		);
		foreach ( $fonts as $rel ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( BLACKROLL_URI . '/' . $rel )
			);
		}

		// Favicon (official mark, traced from the client's logo file).
		printf(
			'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
			esc_url( BLACKROLL_URI . '/assets/images/favicon.svg' )
		);
	},
	1
);

/**
 * Enqueue a built ES module bundle (scripts/build.mjs output) if — and only
 * if — it has actually been built. `motion.js` and `product-3d.js` both
 * contain real `import` statements (lottie-web, three) that do not resolve
 * as bare specifiers in a plain browser, so unlike the vanilla scripts below
 * they cannot be enqueued from source in an un-built dev environment. If
 * `npm run build` hasn't run yet, this silently enqueues nothing — every
 * motion effect it would have powered already has a static fallback in its
 * markup (poster image, fallback text, or nothing at all), so the page is
 * still complete and correct, just without the premium motion.
 *
 * @param string $handle    Script handle.
 * @param string $dist_name File name under assets/js/dist/ (without path).
 * @param array  $deps      Script module dependencies (handles).
 */
function blackroll_enqueue_module( $handle, $dist_name, $deps = array() ) {
	$rel  = 'assets/js/dist/' . $dist_name;
	$path = BLACKROLL_DIR . '/' . $rel;
	if ( ! file_exists( $path ) ) {
		return;
	}
	$src = BLACKROLL_URI . '/' . $rel;
	$ver = BLACKROLL_VERSION . '.' . filemtime( $path );

	if ( function_exists( 'wp_enqueue_script_module' ) ) {
		// WP 6.5+ Script Modules API — the correct, native way to load ESM.
		wp_enqueue_script_module( $handle, $src, $deps, $ver );
		return;
	}

	// Fallback for older WP: classic enqueue + force type="module" on this handle.
	wp_enqueue_script( $handle, $src, array(), $ver, array( 'in_footer' => true ) );
	add_filter(
		'script_loader_tag',
		function ( $tag, $tag_handle ) use ( $handle ) {
			if ( $tag_handle !== $handle ) {
				return $tag;
			}
			return str_replace( ' src=', ' type="module" src=', $tag );
		},
		10,
		2
	);
}

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

		// Motion loader: lazy Lottie (real player, bundled) + gated hero video.
		// Deferred so it never blocks paint; self-limits on prefers-reduced-motion
		// and slow/metered connections (see assets/js/motion.js).
		blackroll_enqueue_module( 'blackroll-motion', 'motion.js' );

		// Product-page 3D showcase (client decision 2026-09 — premium look over
		// loading-speed score). Only where the theme actually renders a
		// [data-blackroll-3d] container, so it never loads elsewhere.
		// Block themes store the template slug without ".html" ("template-product");
		// the old ".html"-only check never matched, so this script never loaded.
		if ( is_page_template( array( 'template-product', 'template-product.html' ) ) ) {
			blackroll_enqueue_module( 'blackroll-product-3d', 'product-3d.js' );
		}

		// Homepage hero "The Blind Reveal" (Fase 2). The entry is ~1.5 KB; it
		// fetches three.js only after idle and only if the device passes the
		// 3D gate (assets/js/three-gate.js).
		if ( is_front_page() ) {
			blackroll_enqueue_module( 'blackroll-home-hero-3d', 'home-hero-3d.js' );
		}
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
