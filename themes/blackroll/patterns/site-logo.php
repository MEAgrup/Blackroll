<?php
/**
 * Title: Site Logo
 * Slug: blackroll/site-logo
 * Inserter: no
 * Description: Official Blackroll lockup (inline SVG, inherits text colour) linked to the homepage.
 * Replaces core/site-title, which printed the WordPress "Site Title" option — set to the bare
 * domain on production, so the header read "BLACKROLLBLINDS.COM" instead of the brand.
 *
 * @package Blackroll
 */

$blackroll_logo = file_get_contents( get_theme_file_path( 'assets/images/logo-horizontal.svg' ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
?>
<!-- wp:html -->
<a class="blackroll-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<?php echo $blackroll_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG shipped with the theme. ?>
</a>
<!-- /wp:html -->
