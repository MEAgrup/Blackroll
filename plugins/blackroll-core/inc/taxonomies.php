<?php
/**
 * Taxonomies: color_series (on shade), project_type (on project).
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register taxonomies and seed default terms.
 */
function blackroll_register_taxonomies() {
	register_taxonomy(
		'color_series',
		'shade',
		array(
			'labels'            => array(
				'name'          => __( 'Color Series', 'blackroll-core' ),
				'singular_name' => __( 'Color Series', 'blackroll-core' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'seri-warna', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'project_type',
		'project',
		array(
			'labels'            => array(
				'name'          => __( 'Project Types', 'blackroll-core' ),
				'singular_name' => __( 'Project Type', 'blackroll-core' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'tipe-proyek', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'blackroll_register_taxonomies' );

/**
 * Ensure the locked default terms exist (idempotent).
 */
add_action(
	'init',
	function () {
		$defaults = array(
			'color_series' => array(
				'black-series' => 'Black Series',
				'white-series' => 'White Series',
			),
			'project_type' => array(
				'residential' => 'Residensial',
				'office'      => 'Kantor',
				'apartment'   => 'Apartemen',
			),
		);
		foreach ( $defaults as $tax => $terms ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			foreach ( $terms as $slug => $name ) {
				if ( ! term_exists( $slug, $tax ) ) {
					wp_insert_term( $name, $tax, array( 'slug' => $slug ) );
				}
			}
		}
	},
	20
);

/**
 * Register taxonomies with Polylang for translation.
 */
add_filter(
	'pll_get_taxonomies',
	function ( $taxes, $is_settings ) {
		unset( $is_settings );
		$taxes['color_series'] = 'color_series';
		$taxes['project_type'] = 'project_type';
		return $taxes;
	},
	10,
	2
);
