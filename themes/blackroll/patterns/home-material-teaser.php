<?php
/**
 * Title: Home — Material & Color Teaser
 * Slug: blackroll/home-material-teaser
 * Categories: blackroll-home
 * Description: Blackout vs Solar Screen × Black/White series teaser → Material page.
 *
 * @package Blackroll
 */
?>
<!-- wp:group {"tagName":"section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"fontSize":"xx-large"} -->
	<h2 class="wp-block-heading has-xx-large-font-size"><?php esc_html_e( 'Material & Warna', 'blackroll' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"large"} -->
			<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Blackout', 'blackroll' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"grey-text"} -->
			<p class="has-grey-text-color has-text-color"><?php esc_html_e( 'Blokir cahaya 100%, konstruksi 5 lapis.', 'blackroll' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"large"} -->
			<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Solar Screen', 'blackroll' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"grey-text"} -->
			<p class="has-grey-text-color has-text-color"><?php esc_html_e( 'Menyaring cahaya, pandangan ke luar tetap terjaga.', 'blackroll' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
	<!-- wp:paragraph -->
	<p><a href="/material-warna/"><?php esc_html_e( 'Lihat Pilihan Material & Warna', 'blackroll' ); ?></a></p>
	<!-- /wp:paragraph -->
</section>
<!-- /wp:group -->
