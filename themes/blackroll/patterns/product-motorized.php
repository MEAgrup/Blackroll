<?php
/**
 * Title: Product — Motorized Blinds
 * Slug: blackroll/product-motorized
 * Categories: blackroll-product
 * Description: Motorized Blinds page body (shares the Product template with Manual).
 *
 * @package Blackroll
 */
?>
<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Deskripsi Produk', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Motorized Blinds Blackroll adalah tier premium/convenience — roller blinds bermotor yang dikontrol dengan remote, ditenagai motor Dooya yang halus, senyap, dan andal. Sama seperti seri Manual: tahan air & minyak, anti-bakteri, anti-jamur, desain minimalis, dan banyak pilihan warna.', 'blackroll' ); ?></p>
<!-- /wp:paragraph -->

<?php /* Premium 3D showcase (client decision 2026-09) — real model/texture pending from the rollerblind team (feedback #5); mount3D() ships a generic monochrome placeholder scene. Gated by assets/js/product-3d.js: falls back to the text below on prefers-reduced-motion, no WebGL, or a slow/metered connection. */ ?>
<!-- wp:html -->
<div class="blackroll-3d-slot" data-blackroll-3d>
	<p class="blackroll-3d-slot__fallback"><?php esc_html_e( 'Pratinjau 3D interaktif — tampil di perangkat yang mendukung.', 'blackroll' ); ?></p>
</div>
<!-- /wp:html -->

<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Keunggulan Produk', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Kontrol Remote', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Buka/tutup satu sentuhan — praktis untuk jendela tinggi atau lebar.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Motor Dooya', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Operasi halus, senyap, dan andal.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Aman untuk Anak', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Tanpa rantai menjuntai — lebih aman untuk keluarga.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Tahan Air & Minyak', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Anti-bakteri & anti-jamur, desain minimalis.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Sistem Penggerak — Motor Dooya', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Ditenagai motor Dooya yang halus, senyap, dan andal, dengan operasi remote. Ideal untuk jendela yang sulit dijangkau dan untuk kenyamanan maksimal di hunian modern.', 'blackroll' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Material', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Blackout', 'blackroll' ); ?></strong> — <?php esc_html_e( 'blokir cahaya 100%, konstruksi 5 lapis.', 'blackroll' ); ?> <strong><?php esc_html_e( 'Solar Screen', 'blackroll' ); ?></strong> — <?php esc_html_e( 'menyaring cahaya, pandangan ke luar tetap terjaga.', 'blackroll' ); ?> <a href="/material-warna/"><?php esc_html_e( 'Lihat Material & Warna', 'blackroll' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Ukuran (cm):', 'blackroll' ); ?></strong> 60 · 80 · 100 · 120</p>
<!-- /wp:paragraph -->

<?php /* [FUTURE / Phase 2] Kalkulator Harga slot — reserved, empty in Phase 1 (Module 4/5). */ ?>
<!-- wp:group {"className":"blackroll-calc-slot","layout":{"type":"constrained"}} --><div class="wp-block-group blackroll-calc-slot"></div><!-- /wp:group -->
