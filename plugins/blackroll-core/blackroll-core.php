<?php
/**
 * Plugin Name:       Blackroll Core
 * Plugin URI:        https://blackroll.co.id
 * Description:        Companion plugin for the Blackroll theme. Registers the shade/project content model, config flags (Sebari submit mode, analytics, primary WA), localized rewrites, schema, security headers and the Sebari lead-form helpers. Lives outside the theme so content and config survive theme swaps.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            MEA Agency
 * License:           GPL-2.0-or-later
 * Text Domain:       blackroll-core
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BLACKROLL_CORE_VERSION', '0.1.0' );
define( 'BLACKROLL_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLACKROLL_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * -----------------------------------------------------------------------------
 * Locked config flags (Amendment 01, Decision Register D1–D6).
 * Each is overridable via a constant in wp-config.php or a filter, so no
 * re-architecture is needed to flip a decision.
 * -----------------------------------------------------------------------------
 */
if ( ! defined( 'BLACKROLL_SITE_URL' ) ) {
	define( 'BLACKROLL_SITE_URL', 'https://blackroll.co.id' );
}
if ( ! defined( 'BLACKROLL_SEBARI_SUBMIT_MODE' ) ) {
	// D1 = redirect (Mode R). Contingency: 'fetch' (Mode F) if Sebari has no redirect_url.
	define( 'BLACKROLL_SEBARI_SUBMIT_MODE', 'redirect' );
}
if ( ! defined( 'BLACKROLL_ANALYTICS_MODE' ) ) {
	// D2 = none (GSC-only). Flip to 'ga4' to re-enable the single-event path.
	define( 'BLACKROLL_ANALYTICS_MODE', 'none' );
}
if ( ! defined( 'BLACKROLL_CSP_REPORT_ONLY' ) ) {
	// Roll out CSP in report-only first (Module 11), flip to false to enforce.
	define( 'BLACKROLL_CSP_REPORT_ONLY', true );
}

require_once BLACKROLL_CORE_DIR . 'inc/config.php';
require_once BLACKROLL_CORE_DIR . 'inc/cpt.php';
require_once BLACKROLL_CORE_DIR . 'inc/taxonomies.php';
require_once BLACKROLL_CORE_DIR . 'inc/meta.php';
require_once BLACKROLL_CORE_DIR . 'inc/fields.php';
require_once BLACKROLL_CORE_DIR . 'inc/rewrites.php';
require_once BLACKROLL_CORE_DIR . 'inc/query.php';
require_once BLACKROLL_CORE_DIR . 'inc/schema.php';
require_once BLACKROLL_CORE_DIR . 'inc/security.php';
require_once BLACKROLL_CORE_DIR . 'inc/sebari.php';
require_once BLACKROLL_CORE_DIR . 'inc/selector.php';
require_once BLACKROLL_CORE_DIR . 'inc/portfolio.php';
require_once BLACKROLL_CORE_DIR . 'inc/settings-page.php';

/**
 * Flush rewrite rules on activation/deactivation so CPT archives and localized
 * bases resolve immediately.
 */
register_activation_hook(
	__FILE__,
	function () {
		blackroll_register_cpts();
		blackroll_register_taxonomies();
		blackroll_register_rewrites();
		flush_rewrite_rules();
	}
);
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
