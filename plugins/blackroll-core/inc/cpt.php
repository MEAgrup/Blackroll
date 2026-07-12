<?php
/**
 * Custom post types: shade (Material & Color) and project (Portfolio).
 * All expose show_in_rest for the block editor AND MCP content-entry (PATCH-18).
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the shade + project CPTs.
 */
function blackroll_register_cpts() {
	register_post_type(
		'shade',
		array(
			'labels'        => array(
				'name'          => __( 'Shades', 'blackroll-core' ),
				'singular_name' => __( 'Shade', 'blackroll-core' ),
				'add_new_item'  => __( 'Add New Shade', 'blackroll-core' ),
				'edit_item'     => __( 'Edit Shade', 'blackroll-core' ),
				'menu_name'     => __( 'Shades', 'blackroll-core' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-art',
			'menu_position' => 26,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'rewrite'       => array( 'slug' => 'warna', 'with_front' => false ),
		)
	);

	register_post_type(
		'project',
		array(
			'labels'        => array(
				'name'          => __( 'Projects', 'blackroll-core' ),
				'singular_name' => __( 'Project', 'blackroll-core' ),
				'add_new_item'  => __( 'Add New Project', 'blackroll-core' ),
				'edit_item'     => __( 'Edit Project', 'blackroll-core' ),
				'menu_name'     => __( 'Portfolio', 'blackroll-core' ),
			),
			'public'        => true,
			'has_archive'   => 'portofolio', // Localized base handled in rewrites.php.
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-format-gallery',
			'menu_position' => 25,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
			'rewrite'       => array( 'slug' => 'portofolio', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'blackroll_register_cpts' );

/**
 * Make both CPTs translatable in Polylang when it is active.
 */
add_filter(
	'pll_get_post_types',
	function ( $types, $is_settings ) {
		unset( $is_settings );
		$types['shade']   = 'shade';
		$types['project'] = 'project';
		return $types;
	},
	10,
	2
);
