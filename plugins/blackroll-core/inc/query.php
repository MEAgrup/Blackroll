<?php
/**
 * Query loop integration: the homepage "Portfolio Highlights" pattern passes
 * blackrollFeatured=true; translate it to a meta_query for featured projects.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end query variation for the Query Loop block.
 */
add_filter(
	'query_loop_block_query_vars',
	function ( $query, $block ) {
		$attrs = isset( $block->context['query'] ) ? $block->context['query'] : array();
		if ( ! empty( $attrs['blackrollFeatured'] ) ) {
			$query['meta_query'] = array(
				array(
					'key'   => 'featured',
					'value' => '1',
				),
			);
		}
		return $query;
	},
	10,
	2
);

/**
 * REST rendering of the same block (editor preview / server render) honours the
 * featured flag too.
 */
add_filter(
	'rest_project_query',
	function ( $args, $request ) {
		if ( 'true' === $request->get_param( 'blackroll_featured' ) ) {
			$args['meta_key']   = 'featured';
			$args['meta_value'] = '1';
		}
		return $args;
	},
	10,
	2
);
