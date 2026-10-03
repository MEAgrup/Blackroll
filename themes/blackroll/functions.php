<?php
/**
 * Blackroll theme bootstrap.
 *
 * Presentation-only concerns live in the theme. Data model, config flags,
 * security headers, schema and the Sebari submit handling live in the
 * companion plugin `blackroll-core` (so they survive a theme swap).
 *
 * @package Blackroll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BLACKROLL_VERSION', '0.1.0' );
define( 'BLACKROLL_DIR', get_template_directory() );
define( 'BLACKROLL_URI', get_template_directory_uri() );

require_once BLACKROLL_DIR . '/inc/setup.php';
require_once BLACKROLL_DIR . '/inc/enqueue.php';
require_once BLACKROLL_DIR . '/inc/performance.php';
require_once BLACKROLL_DIR . '/inc/block-patterns.php';
require_once BLACKROLL_DIR . '/inc/template-helpers.php';
require_once BLACKROLL_DIR . '/inc/hooks.php';
