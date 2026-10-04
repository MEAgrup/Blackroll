<?php
/**
 * Title: Home — Product Preview
 * Slug: blackroll/home-product-preview
 * Categories: blackroll-home
 * Description: Manual vs Motorized as two photo cards. Also used as the /produk/ page body.
 *
 * @package Blackroll
 */
$blackroll_img      = get_template_directory_uri() . '/assets/images/collection/';
$blackroll_products = array(
	array(
		'/produk/blinds-manual/',
		'hero-clean.webp',
		1341,
		1600,
		__( 'Manual Blinds', 'blackroll' ),
		__( 'Operasi rantai — hemat biaya, tanpa kabel, andal untuk penggunaan harian.', 'blackroll' ),
	),
	array(
		'/produk/blinds-motorized/',
		'charcoal-linen-detail.webp',
		1600,
		898,
		__( 'Motorized Blinds', 'blackroll' ),
		__( 'Motor Dooya — halus dan senyap, dikontrol dengan remote. Ideal untuk jendela tinggi.', 'blackroll' ),
	),
);
?>
<!-- wp:group {"align":"full","tagName":"section","className":"blackroll-products","backgroundColor":"stone","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull blackroll-products has-stone-background-color has-background">
	<!-- wp:html -->
	<div class="blackroll-section-head alignwide">
		<p class="blackroll-eyebrow"><?php esc_html_e( 'Sistem penggerak', 'blackroll' ); ?></p>
		<h2 class="has-xx-large-font-size"><?php esc_html_e( 'Manual atau', 'blackroll' ); ?> <em><?php esc_html_e( 'Motorized', 'blackroll' ); ?></em></h2>
	</div>
	<div class="blackroll-products__grid alignwide">
		<?php foreach ( $blackroll_products as $p ) : ?>
			<a class="blackroll-product blackroll-reveal" href="<?php echo esc_url( home_url( $p[0] ) ); ?>">
				<span class="blackroll-product__media"><img src="<?php echo esc_url( $blackroll_img . $p[1] ); ?>" alt="<?php echo esc_attr( $p[4] ); ?>" width="<?php echo (int) $p[2]; ?>" height="<?php echo (int) $p[3]; ?>" loading="lazy" decoding="async"></span>
				<span class="blackroll-product__body">
					<span class="blackroll-product__title"><?php echo esc_html( $p[4] ); ?></span>
					<span class="blackroll-product__text"><?php echo esc_html( $p[5] ); ?></span>
					<span class="blackroll-link"><?php esc_html_e( 'Lihat detail', 'blackroll' ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
