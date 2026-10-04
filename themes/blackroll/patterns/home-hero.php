<?php
/**
 * Title: Home — Hero
 * Slug: blackroll/home-hero
 * Categories: blackroll-home
 * Description: "The Blind Reveal" (Fase 2). Full-height dark hero: copy + CTA on the left,
 * a 3D roller blind on the right (assets/js/home-hero-3d.js). The H1 is real HTML outside
 * the canvas (SEO + LCP). Without WebGL / with reduced motion / on a slow connection the
 * stage shows a real product photo and the colour chips swap that photo instead.
 *
 * @package Blackroll
 */

$blackroll_img = get_template_directory_uri() . '/assets/images/collection/';
$blackroll_wa  = function_exists( 'blackroll_wa_link' ) ? blackroll_wa_link() : home_url( '/kontak/' );

// Roller is the main line (client, 2026-10-01); Zebra is the secondary system.
$blackroll_chips = array(
	array( 'charcoal-linen', __( 'Charcoal Linen', 'blackroll' ), 'roller' ),
	array( 'taupe', __( 'Taupe', 'blackroll' ), 'roller' ),
	array( 'grey', __( 'Grey', 'blackroll' ), 'roller' ),
	array( 'beige', __( 'Beige', 'blackroll' ), 'roller' ),
	array( 'sky', __( 'Sky Blue', 'blackroll' ), 'roller' ),
	array( 'snow-texture', __( 'Snow', 'blackroll' ), 'roller' ),
	array( 'zebra-ivory', __( 'Zebra Ivory', 'blackroll' ), 'zebra' ),
	array( 'zebra-white', __( 'Zebra White', 'blackroll' ), 'zebra' ),
	array( 'zebra-s004', __( 'Zebra S004', 'blackroll' ), 'zebra' ),
);
?>
<!-- wp:group {"align":"full","tagName":"section","className":"blackroll-hero blackroll-dark","backgroundColor":"black","textColor":"white","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull blackroll-hero blackroll-dark has-white-color has-black-background-color has-text-color has-background" data-blackroll-hero>
	<!-- wp:html -->
	<div class="blackroll-hero__inner">
		<div class="blackroll-hero__copy">
			<p class="blackroll-eyebrow"><?php esc_html_e( 'Roller Blinds · Black is Cool', 'blackroll' ); ?></p>
			<h1 class="blackroll-hero__title"><?php esc_html_e( 'Blinds Premium Terjangkau untuk', 'blackroll' ); ?> <em><?php esc_html_e( 'Hunian Modern', 'blackroll' ); ?></em></h1>
			<p class="blackroll-hero__lead"><?php esc_html_e( 'Roller blinds tahan air & minyak, anti-bakteri, anti-jamur — dengan pilihan Manual dan Motorized. Dipercaya untuk proyek residence dan commercial.', 'blackroll' ); ?></p>
			<div class="blackroll-hero__actions">
				<a class="wp-element-button blackroll-btn--light" href="<?php echo esc_url( $blackroll_wa ); ?>" rel="noopener"><?php esc_html_e( 'Konsultasi Gratis', 'blackroll' ); ?></a>
				<a class="blackroll-btn--ghost" href="<?php echo esc_url( home_url( '/material-warna/' ) ); ?>"><?php esc_html_e( 'Lihat 24 Pilihan Warna', 'blackroll' ); ?></a>
			</div>
		</div>

		<div class="blackroll-hero__showcase">
			<div class="blackroll-hero__stage" data-blackroll-hero-stage>
				<img class="blackroll-hero__fallback" src="<?php echo esc_url( $blackroll_img . 'charcoal-linen-front.webp' ); ?>" alt="<?php esc_attr_e( 'Roller blind Blackroll warna charcoal linen', 'blackroll' ); ?>" width="1284" height="1600" fetchpriority="low" decoding="async">
			</div>
			<div class="blackroll-hero__controls">
				<div class="blackroll-hero__modes" role="group" aria-label="<?php esc_attr_e( 'Sistem blind', 'blackroll' ); ?>">
					<button type="button" data-hero-mode="roller" aria-pressed="true"><?php esc_html_e( 'Roller', 'blackroll' ); ?></button>
					<button type="button" data-hero-mode="zebra" aria-pressed="false"><?php esc_html_e( 'Zebra', 'blackroll' ); ?></button>
				</div>
				<div class="blackroll-hero__chips" role="group" aria-label="<?php esc_attr_e( 'Pilih warna', 'blackroll' ); ?>">
					<?php foreach ( $blackroll_chips as $i => $chip ) : ?>
						<button type="button" class="blackroll-hero__chip" data-hero-swatch
							data-mode="<?php echo esc_attr( $chip[2] ); ?>"
							data-label="<?php echo esc_attr( $chip[1] ); ?>"
							data-texture="<?php echo esc_url( $blackroll_img . $chip[0] . '-swatch.webp' ); ?>"
							data-front="<?php echo esc_url( $blackroll_img . $chip[0] . '-front.webp' ); ?>"
							style="background-image:url(<?php echo esc_url( $blackroll_img . $chip[0] . '-swatch.webp' ); ?>)"
							aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"
							aria-label="<?php echo esc_attr( $chip[1] ); ?>"
							<?php echo 'zebra' === $chip[2] ? 'hidden' : ''; ?>></button>
					<?php endforeach; ?>
				</div>
				<p class="blackroll-hero__label" data-hero-label aria-live="polite"><?php esc_html_e( 'Charcoal Linen', 'blackroll' ); ?></p>
			</div>
		</div>
	</div>
	<p class="blackroll-hero__scroll" aria-hidden="true"><?php esc_html_e( 'Scroll — lihat blind tergulung', 'blackroll' ); ?></p>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
