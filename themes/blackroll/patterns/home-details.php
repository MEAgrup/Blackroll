<?php
/**
 * Title: Home — Craft details
 * Slug: blackroll/home-details
 * Categories: blackroll-home
 * Description: Dark section of hardware close-ups (bracket, bottom bar, chain) from the
 * client shoot — the "why it is premium" proof.
 *
 * @package Blackroll
 */
$blackroll_img     = get_template_directory_uri() . '/assets/images/collection/';
$blackroll_details = array(
	array( 'detail-chain.webp', 1600, 1321, __( 'Bracket kokoh', 'blackroll' ), __( 'Dudukan logam hitam, terpasang rapi di dinding atau kusen.', 'blackroll' ) ),
	array( 'detail-bottom-bar.webp', 1600, 898, __( 'Bottom bar beraksen logo', 'blackroll' ), __( 'Pemberat aluminium hitam menjaga kain jatuh lurus.', 'blackroll' ) ),
	array( 'detail-bracket.webp', 898, 1600, __( 'Rantai halus', 'blackroll' ), __( 'Mekanisme manual yang ringan — atau upgrade ke motor Dooya.', 'blackroll' ) ),
);
?>
<!-- wp:group {"align":"full","tagName":"section","className":"blackroll-details blackroll-dark","backgroundColor":"black","textColor":"white","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull blackroll-details blackroll-dark has-white-color has-black-background-color has-text-color has-background">
	<!-- wp:html -->
	<div class="blackroll-section-head alignwide">
		<p class="blackroll-eyebrow"><?php esc_html_e( 'Detail', 'blackroll' ); ?></p>
		<h2 class="has-xx-large-font-size"><?php esc_html_e( 'Premium ada di', 'blackroll' ); ?> <em><?php esc_html_e( 'detail kecil', 'blackroll' ); ?></em></h2>
	</div>
	<ul class="blackroll-details__grid alignwide">
		<?php foreach ( $blackroll_details as $d ) : ?>
			<li class="blackroll-detail blackroll-reveal">
				<span class="blackroll-detail__media"><img src="<?php echo esc_url( $blackroll_img . $d[0] ); ?>" alt="<?php echo esc_attr( $d[3] ); ?>" width="<?php echo (int) $d[1]; ?>" height="<?php echo (int) $d[2]; ?>" loading="lazy" decoding="async"></span>
				<h3 class="blackroll-detail__title"><?php echo esc_html( $d[3] ); ?></h3>
				<p class="blackroll-detail__text"><?php echo esc_html( $d[4] ); ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
