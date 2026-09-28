<?php
/**
 * Title: Home — Product Preview
 * Slug: blackroll/home-product-preview
 * Categories: blackroll-home
 * Description: Two cards — Manual & Motorized — each with Detail + Contact links.
 *
 * @package Blackroll
 */
?>
<!-- wp:group {"align":"full","tagName":"section","className":"blackroll-dark","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"charcoal","textColor":"white","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull blackroll-dark has-white-color has-charcoal-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","fontSize":"xx-large"} -->
	<h2 class="wp-block-heading has-text-align-center has-xx-large-font-size"><?php esc_html_e( 'Pilih Sistem Penggerak', 'blackroll' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"x-large"} -->
			<h3 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Manual Blinds', 'blackroll' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"grey"} -->
			<p class="has-grey-color has-text-color"><?php esc_html_e( 'Operasi rantai, hemat biaya, tanpa kabel, andal.', 'blackroll' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p><a href="/produk/blinds-manual/"><?php esc_html_e( 'Lihat Detail', 'blackroll' ); ?></a> · <a href="/kontak/"><?php esc_html_e( 'Hubungi Kami', 'blackroll' ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"x-large"} -->
			<h3 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Motorized Blinds', 'blackroll' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"grey"} -->
			<p class="has-grey-color has-text-color"><?php esc_html_e( 'Motor Dooya — halus, senyap, andal. Kontrol remote.', 'blackroll' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p><a href="/produk/blinds-motorized/"><?php esc_html_e( 'Lihat Detail', 'blackroll' ); ?></a> · <a href="/kontak/"><?php esc_html_e( 'Hubungi Kami', 'blackroll' ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
