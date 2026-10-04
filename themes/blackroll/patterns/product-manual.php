<?php
/**
 * Title: Product — Manual Blinds
 * Slug: blackroll/product-manual
 * Categories: blackroll-product
 * Description: Manual Blinds page body (shares the Product template with Motorized).
 *
 * @package Blackroll
 */
?>
<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Deskripsi Produk', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Manual Blinds Blackroll adalah roller blinds operasi rantai (chain) — solusi premium terjangkau untuk mengatur cahaya dan privasi. Diproduksi dengan teknologi terkini, tahan air & minyak, anti-bakteri, dan anti-jamur, dengan desain minimalis yang cocok untuk hunian modern, kantor, maupun apartemen.', 'blackroll' ); ?></p>
<!-- /wp:paragraph -->

<?php
// Client shoot, Sept 2026 (assets/images/collection/README.md).
echo "<!-- wp:html -->\n" . blackroll_product_gallery( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_* helpers.
	array(
		array( 'hero-clean.webp', __( 'Roller blind Blackroll terpasang, sistem rantai', 'blackroll' ) ),
		array( 'detail-bottom-bar.webp', __( 'Bottom bar aluminium hitam dengan logo Blackroll', 'blackroll' ) ),
		array( 'detail-bracket.webp', __( 'Mekanisme rantai manual dan tabung penggulung', 'blackroll' ) ),
		array( 'detail-chain.webp', __( 'Bracket hitam roller blind', 'blackroll' ) ),
	)
) . "\n<!-- /wp:html -->\n";
?>

<?php /* 3D showcase (Fase 2): the same blind scene as the homepage hero (assets/js/blind-scene.js), with real fabric from the client shoot. Gated by assets/js/three-gate.js — on reduced motion, no WebGL or a slow connection the photo below stays and three.js is never fetched. */ ?>
<!-- wp:html -->
<figure class="blackroll-3d">
	<div class="blackroll-3d-slot" data-blackroll-3d data-chain="true" data-texture="<?php echo esc_url( get_template_directory_uri() . '/assets/images/collection/charcoal-linen-swatch.webp' ); ?>">
		<img class="blackroll-3d-slot__fallback" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/collection/charcoal-linen-front.webp' ); ?>" alt="<?php esc_attr_e( 'Roller blind Blackroll warna charcoal linen', 'blackroll' ); ?>" width="1284" height="1600" loading="lazy" decoding="async">
	</div>
	<label class="blackroll-3d__range" data-blackroll-3d-range hidden>
		<span><?php esc_html_e( 'Geser untuk menggulung blind', 'blackroll' ); ?></span>
		<input type="range" min="0" max="100" value="0">
	</label>
</figure>
<!-- /wp:html -->

<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Keunggulan Produk', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Teknologi Terkini', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Diproduksi dengan proses dan material generasi terbaru.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Tahan Air & Minyak', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Anti-bakteri dan anti-jamur — ideal untuk dapur & area lembap.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Desain Minimalis', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Tampilan bersih yang menyatu dengan interior apa pun.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Banyak Pilihan Warna', 'blackroll' ); ?></h3><!-- /wp:heading -->
		<!-- wp:paragraph --><p><?php esc_html_e( 'Black Series & White Series dengan beragam shade.', 'blackroll' ); ?></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Sistem Penggerak — Rantai (Manual)', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Dioperasikan dengan rantai: hemat biaya, tanpa kabel listrik, dan sangat andal. Pilihan tepat untuk sebagian besar jendela rumah dan kantor.', 'blackroll' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"fontSize":"small","textColor":"grey-text"} -->
<p class="has-grey-text-color has-text-color has-small-font-size"><?php esc_html_e( 'Catatan keselamatan: jauhkan rantai dari jangkauan anak-anak.', 'blackroll' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Material', 'blackroll' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Blackout', 'blackroll' ); ?></strong> — <?php esc_html_e( 'blokir cahaya 100%, konstruksi 5 lapis (perlindungan tahan air/minyak, color-lock, blackout powder, woven base, inner water protection).', 'blackroll' ); ?> <strong><?php esc_html_e( 'Solar Screen', 'blackroll' ); ?></strong> — <?php esc_html_e( 'menyaring cahaya, pandangan ke luar tetap terjaga.', 'blackroll' ); ?> <a href="/material-warna/"><?php esc_html_e( 'Lihat Material & Warna', 'blackroll' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Ukuran (cm):', 'blackroll' ); ?></strong> 60 · 80 · 100 · 120</p>
<!-- /wp:paragraph -->

<?php /* [FUTURE / Phase 2] Kalkulator Harga slot — reserved, empty in Phase 1 (Module 4). */ ?>
<!-- wp:group {"className":"blackroll-calc-slot","layout":{"type":"constrained"}} --><div class="wp-block-group blackroll-calc-slot"></div><!-- /wp:group -->
