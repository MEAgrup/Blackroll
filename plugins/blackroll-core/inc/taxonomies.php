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
			// No public term archive: the Material & Warna selector filters by
			// series client-side (data attributes), it never links to a term
			// archive URL. A public archive here just duplicated the selector's
			// own listing. Admin UI + REST stay on for filtering/MCP.
			'public'            => false,
			'publicly_queryable' => false,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
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
			// Same reasoning as color_series: Portfolio (`[blackroll_portfolio]`)
			// filters by project type client-side, never links to a term archive.
			'public'            => false,
			'publicly_queryable' => false,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
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
