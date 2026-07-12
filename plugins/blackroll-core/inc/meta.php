<?php
/**
 * Post meta for shade + project. All show_in_rest => true (PATCH-06, PATCH-18):
 * required for the block editor, block bindings AND MCP content-entry.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta schema for the shade CPT.
 *
 * @return array<string,array>
 */
function blackroll_shade_meta_schema() {
	return array(
		'sku_code'         => array( 'type' => 'string', 'label' => __( 'SKU Code', 'blackroll-core' ) ),
		'swatch_image'     => array( 'type' => 'integer', 'label' => __( 'Swatch Image (ID)', 'blackroll-core' ) ),
		'preview_image'    => array( 'type' => 'integer', 'label' => __( 'Preview Image (ID)', 'blackroll-core' ) ),
		'preview_blackout' => array( 'type' => 'integer', 'label' => __( 'Preview — Blackout (ID)', 'blackroll-core' ) ),
		'preview_solar'    => array( 'type' => 'integer', 'label' => __( 'Preview — Solar Screen (ID)', 'blackroll-core' ) ),
		'material_blackout' => array( 'type' => 'boolean', 'label' => __( 'Available in Blackout', 'blackroll-core' ) ),
		'material_solar'    => array( 'type' => 'boolean', 'label' => __( 'Available in Solar Screen', 'blackroll-core' ) ),
	);
}

/**
 * Meta schema for the project CPT.
 *
 * @return array<string,array>
 */
function blackroll_project_meta_schema() {
	return array(
		'caption'      => array( 'type' => 'string', 'label' => __( 'Caption', 'blackroll-core' ) ),
		'product_used' => array( 'type' => 'string', 'label' => __( 'Product Used', 'blackroll-core' ) ),
		'location'     => array( 'type' => 'string', 'label' => __( 'Location', 'blackroll-core' ) ),
		'featured'     => array( 'type' => 'boolean', 'label' => __( 'Featured (Homepage highlight)', 'blackroll-core' ) ),
		'gallery_ids'  => array( 'type' => 'string', 'label' => __( 'Gallery Image IDs (comma-separated)', 'blackroll-core' ) ),
	);
}

/**
 * Register all meta with REST exposure and auth guards.
 */
add_action(
	'init',
	function () {
		$register = function ( $post_type, $schema ) {
			foreach ( $schema as $key => $def ) {
				register_post_meta(
					$post_type,
					$key,
					array(
						'type'          => $def['type'],
						'single'        => true,
						'show_in_rest'  => true,
						'auth_callback' => function () {
							return current_user_can( 'edit_posts' );
						},
						'sanitize_callback' => blackroll_meta_sanitizer( $def['type'] ),
					)
				);
			}
		};
		$register( 'shade', blackroll_shade_meta_schema() );
		$register( 'project', blackroll_project_meta_schema() );
	}
);

/**
 * Return a sanitizer callback for a meta type.
 *
 * @param string $type Meta type.
 * @return callable
 */
function blackroll_meta_sanitizer( $type ) {
	switch ( $type ) {
		case 'integer':
			return 'absint';
		case 'boolean':
			return function ( $v ) {
				return (bool) $v;
			};
		default:
			return 'sanitize_text_field';
	}
}
