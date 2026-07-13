<?php
/**
 * Title: Page — Contact
 * Slug: blackroll/page-contact
 * Categories: blackroll-general
 * Description: Contact page body — Sebari lead form + always-visible fallback + business info + static map link.
 *
 * @package Blackroll
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"55%"} -->
		<div class="wp-block-column" style="flex-basis:55%">
			<!-- wp:paragraph {"fontSize":"large"} -->
			<p class="has-large-font-size"><?php esc_html_e( 'Konsultasi gratis, respon cepat via WhatsApp. Isi form di bawah — tim kami akan menghubungi Anda.', 'blackroll' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:shortcode -->[blackroll_sebari_form]<!-- /wp:shortcode -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"45%"} -->
		<div class="wp-block-column" style="flex-basis:45%">
			<!-- wp:shortcode -->[blackroll_fallback_contact]<!-- /wp:shortcode -->

			<!-- wp:heading {"level":3,"fontSize":"large","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
			<h3 class="wp-block-heading has-large-font-size" style="margin-top:var(--wp--preset--spacing--40)"><?php esc_html_e( 'Kunjungi Kami', 'blackroll' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Jl. Gatot Subroto No.107, Samoja,<br>Batununggal, Kota Bandung, Jawa Barat 40273<br><?php esc_html_e( 'Buka setiap hari · Tutup 20.30', 'blackroll' ); ?></p>
			<!-- /wp:paragraph -->

			<?php // Static map image (WebP export) + external link — no Maps JS/API (Module 11 OA#3). ?>
			<!-- wp:group {"className":"blackroll-map","layout":{"type":"constrained"}} -->
			<div class="wp-block-group blackroll-map">
				<!-- wp:image {"className":"blackroll-map__img"} -->
				<figure class="wp-block-image blackroll-map__img"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/map-placeholder.svg' ); ?>" alt="<?php esc_attr_e( 'Peta lokasi toko Blackroll di Bandung', 'blackroll' ); ?>" width="600" height="360" loading="lazy" decoding="async"/></figure>
				<!-- /wp:image -->
				<!-- wp:paragraph -->
				<p><a href="https://maps.google.com/?q=Jl.+Gatot+Subroto+No.107+Bandung" rel="noopener"><?php esc_html_e( 'Buka di Google Maps', 'blackroll' ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
