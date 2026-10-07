<?php
/**
 * Schema & breadcrumbs.
 *
 * Rank Math Lite handles most page schema (Product, Article, etc.). This file
 * provides the pieces the theme needs directly: a breadcrumbs shortcode with
 * BreadcrumbList JSON-LD, and an Organization/LocalBusiness block on
 * Home/Contact. Output is filterable so it can defer to Rank Math if desired.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Business constants (single source; WA from options).
 *
 * @return array
 */
function blackroll_business_info() {
	return array(
		'name'     => 'Blackroll',
		'phone'    => blackroll_get_option( 'blackroll_primary_wa' ),
		'street'   => 'Jl. Gatot Subroto No.107, Samoja, Batununggal',
		'city'     => 'Bandung',
		'region'   => 'Jawa Barat',
		'postal'   => '40273',
		'country'  => 'ID',
		'maps_url' => blackroll_get_option( 'blackroll_maps_url' ),
	);
}

/**
 * Emit Organization + LocalBusiness JSON-LD on Home and Contact.
 */
add_action(
	'wp_head',
	function () {
		if ( ! ( is_front_page() || blackroll_is_page_role( 'contact' ) ) ) {
			return;
		}
		if ( ! apply_filters( 'blackroll_emit_localbusiness_schema', true ) ) {
			return; // Let Rank Math own it if a site prefers that.
		}
		$b    = blackroll_business_info();
		$data = array(
			'@context' => 'https://schema.org',
			'@type'    => 'LocalBusiness',
			'name'     => $b['name'],
			'url'      => trailingslashit( BLACKROLL_SITE_URL ),
			'telephone' => $b['phone'],
			'address'  => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $b['street'],
				'addressLocality' => $b['city'],
				'addressRegion'   => $b['region'],
				'postalCode'      => $b['postal'],
				'addressCountry'  => $b['country'],
			),
			'sameAs'   => array(
				'https://instagram.com/blackroll.official',
				'https://www.tiktok.com/@blackroll.blinds',
			),
		);
		echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $data ) . "</script>\n";
	},
	20
);

/**
 * Breadcrumbs shortcode: visual trail + BreadcrumbList JSON-LD.
 * Prefers Rank Math's breadcrumb function when available.
 */
add_shortcode(
	'blackroll_breadcrumbs',
	function () {
		if ( is_front_page() ) {
			return '';
		}
		if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
			ob_start();
			rank_math_the_breadcrumbs();
			return ob_get_clean();
		}

		$items = array();
		$home  = blackroll_is_en() ? 'Home' : 'Beranda';
		$items[] = array( 'name' => $home, 'url' => home_url( '/' ) );

		$obj = get_queried_object();
		if ( $obj instanceof WP_Post ) {
			$items[] = array( 'name' => get_the_title( $obj ), 'url' => get_permalink( $obj ) );
		} elseif ( $obj instanceof WP_Term ) {
			$items[] = array( 'name' => $obj->name, 'url' => get_term_link( $obj ) );
		}

		// Visual trail.
		$parts = array();
		$ld    = array();
		foreach ( $items as $i => $it ) {
			$last    = ( $i === count( $items ) - 1 );
			$parts[] = $last
				? '<span aria-current="page">' . esc_html( $it['name'] ) . '</span>'
				: '<a href="' . esc_url( $it['url'] ) . '">' . esc_html( $it['name'] ) . '</a>';
			$ld[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $it['name'],
				'item'     => $it['url'],
			);
		}
		$json = wp_json_encode(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $ld,
			)
		);

		return '<nav class="blackroll-breadcrumbs" aria-label="Breadcrumb">' . implode( ' <span aria-hidden="true">/</span> ', $parts ) . '</nav>'
			. '<script type="application/ld+json">' . $json . '</script>';
	}
);
