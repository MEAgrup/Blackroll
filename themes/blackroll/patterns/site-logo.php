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
?>
<!-- wp:html -->
<?php echo blackroll_logo( 'horizontal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static theme SVG, escaped URL. ?>
<!-- /wp:html -->
