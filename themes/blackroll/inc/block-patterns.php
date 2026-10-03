<?php
/**
 * Register block pattern categories. Patterns themselves are auto-registered
 * from the /patterns directory by WordPress.
 *
 * @package Blackroll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		$categories = array(
			'blackroll-home'     => __( 'Blackroll — Home', 'blackroll' ),
			'blackroll-product'  => __( 'Blackroll — Product', 'blackroll' ),
			'blackroll-general'  => __( 'Blackroll — General', 'blackroll' ),
			'blackroll-cta'      => __( 'Blackroll — CTA', 'blackroll' ),
		);
		foreach ( $categories as $slug => $label ) {
			register_block_pattern_category( $slug, array( 'label' => $label ) );
		}
	}
);
