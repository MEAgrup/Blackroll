<?php
/**
 * Site-wide UI hooks: skip-to-content link and the bottom-floating CTA.
 * Rendered via PHP hooks (not per-template) so they exist on every page.
 *
 * @package Blackroll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Skip-to-content link — first focusable element (PATCH-13).
 * Templates mark their main region with id="blackroll-content".
 */
add_action(
	'wp_body_open',
	function () {
		$label = ( function_exists( 'pll_current_language' ) && 'en' === pll_current_language() )
			? 'Skip to content'
			: 'Lewati ke konten';
		printf(
			'<a class="blackroll-skip-link" href="#blackroll-content">%s</a>',
			esc_html( $label )
		);
	},
	1
);

/**
 * Bottom-floating CTA on every page (Module 2 Rule 5).
 */
add_action(
	'wp_footer',
	function () {
		if ( function_exists( 'blackroll_render_floating_cta' ) ) {
			blackroll_render_floating_cta();
		}
	}
);
