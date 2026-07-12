<?php
/**
 * Security headers (Module 11 + PATCH-07).
 *
 * CSP is the critical one. Rules:
 *  - form-action MUST include 'self' (PATCH-07) — omitting it silently breaks
 *    wp-login, wp-admin forms and search.
 *  - Sebari POST target allowed via form-action https://sebari.co.id (Mode R).
 *  - connect-src https://sebari.co.id added ONLY when submit mode = fetch (D1
 *    contingency / Mode F).
 *  - Fonts self-hosted → font-src 'self'. Map is a static image + link → no
 *    Google Maps allowlist needed.
 *  - Roll out report-only first (BLACKROLL_CSP_REPORT_ONLY), then enforce.
 *
 * On production these are ideally set at the server (Hostinger/.htaccess); this
 * PHP delivery is the portable default and the source of truth for the policy.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the CSP policy string.
 *
 * @return string
 */
function blackroll_build_csp() {
	$fetch_mode = ( 'fetch' === blackroll_sebari_submit_mode() );

	$directives = array(
		"default-src"     => array( "'self'" ),
		"base-uri"        => array( "'self'" ),
		"object-src"      => array( "'none'" ),
		"frame-ancestors" => array( "'self'" ),
		"img-src"         => array( "'self'", 'data:', 'https:' ),
		"font-src"        => array( "'self'" ),
		"style-src"       => array( "'self'", "'unsafe-inline'" ),
		"script-src"      => array( "'self'" ),
		"form-action"     => array( "'self'", 'https://sebari.co.id' ), // PATCH-07 + Mode R.
	);

	// GA4 (only if analytics mode flips to ga4 later — D2 is currently none).
	if ( 'ga4' === blackroll_analytics_mode() ) {
		$directives['script-src'][] = 'https://www.googletagmanager.com';
		$directives['connect-src'][] = 'https://www.google-analytics.com';
		$directives['connect-src'][] = 'https://region1.google-analytics.com';
	}

	// Mode F (fetch intercept) needs connect-src to Sebari.
	if ( $fetch_mode ) {
		$directives['connect-src'] = array_merge(
			isset( $directives['connect-src'] ) ? $directives['connect-src'] : array( "'self'" ),
			array( 'https://sebari.co.id' )
		);
		if ( ! in_array( "'self'", $directives['connect-src'], true ) ) {
			array_unshift( $directives['connect-src'], "'self'" );
		}
	}

	$directives = apply_filters( 'blackroll_csp_directives', $directives );

	$parts = array();
	foreach ( $directives as $key => $vals ) {
		$parts[] = $key . ' ' . implode( ' ', array_unique( $vals ) );
	}
	return implode( '; ', $parts );
}

/**
 * Send security headers.
 */
add_action(
	'send_headers',
	function () {
		if ( is_admin() ) {
			return;
		}
		$csp    = blackroll_build_csp();
		$header = BLACKROLL_CSP_REPORT_ONLY ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
		header( $header . ': ' . $csp );

		header( 'X-Content-Type-Options: nosniff' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
		// HSTS is best set at the server once HTTPS is confirmed site-wide.
		if ( is_ssl() ) {
			header( 'Strict-Transport-Security: max-age=15552000; includeSubDomains' );
		}
	}
);

/**
 * Hardening: disable XML-RPC, remove file editor, drop the REST user endpoint
 * for unauthenticated requests (Module 11 Rules 1, 5, 6).
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

add_action(
	'init',
	function () {
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}
	}
);

add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'] );
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);
