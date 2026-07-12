<?php
/**
 * Blackroll settings page: primary/secondary WA, maps URL, GA4 id, and a
 * read-only view of the locked config flags (D1–D6).
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'Blackroll', 'blackroll-core' ),
			__( 'Blackroll', 'blackroll-core' ),
			'manage_options',
			'blackroll-settings',
			'blackroll_render_settings_page',
			'dashicons-admin-generic',
			24
		);
	}
);

/**
 * Render the settings page.
 */
function blackroll_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$fields = array(
		'blackroll_primary_wa'   => __( 'Primary WhatsApp (D4)', 'blackroll-core' ),
		'blackroll_secondary_wa' => __( 'Secondary WhatsApp (store — optional)', 'blackroll-core' ),
		'blackroll_maps_url'     => __( 'Google Maps URL (Contact)', 'blackroll-core' ),
		'blackroll_ga4_id'       => __( 'GA4 Measurement ID (only if analytics = ga4)', 'blackroll-core' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Blackroll Settings', 'blackroll-core' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'blackroll_settings' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $key => $label ) : ?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td><input name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( blackroll_get_option( $key ) ); ?>"></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>

		<hr>
		<h2><?php esc_html_e( 'Locked configuration (Amendment 01)', 'blackroll-core' ); ?></h2>
		<p class="description"><?php esc_html_e( 'These are set via constants (overridable in wp-config.php). Shown here for reference.', 'blackroll-core' ); ?></p>
		<table class="widefat striped" style="max-width:640px">
			<tbody>
				<tr><td>Sebari submit mode (D1)</td><td><code><?php echo esc_html( blackroll_sebari_submit_mode() ); ?></code></td></tr>
				<tr><td>Analytics mode (D2)</td><td><code><?php echo esc_html( blackroll_analytics_mode() ); ?></code></td></tr>
				<tr><td>CSP</td><td><code><?php echo esc_html( BLACKROLL_CSP_REPORT_ONLY ? 'report-only' : 'enforce' ); ?></code></td></tr>
				<tr><td>Site URL</td><td><code><?php echo esc_html( BLACKROLL_SITE_URL ); ?></code></td></tr>
			</tbody>
		</table>
	</div>
	<?php
}
