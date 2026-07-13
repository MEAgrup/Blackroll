<?php
/**
 * Title: Page — Material & Color
 * Slug: blackroll/page-material-color
 * Categories: blackroll-general
 * Description: Material & Color page body — intro selling points + interactive selector.
 *
 * @package Blackroll
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30)">
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size"><?php esc_html_e( 'Jelajahi kombinasi material dan warna Blackroll. Blackout dengan konstruksi 5 lapis — tahan air & minyak, anti-bakteri, anti-jamur — atau Solar Screen yang menyaring cahaya sambil menjaga pandangan ke luar.', 'blackroll' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:shortcode -->[blackroll_selector]<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
