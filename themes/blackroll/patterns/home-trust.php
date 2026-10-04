<?php
/**
 * Title: Home — Trust
 * Slug: blackroll/home-trust
 * Categories: blackroll-home
 * Description: Stat strip on the warm stone background. "100+ proyek" and "ready stock"
 * approved by the client (2026-10-02).
 *
 * @package Blackroll
 */
$blackroll_stats = array(
	array( '100+', __( 'Proyek selesai — residence, kantor, apartemen', 'blackroll' ) ),
	array( '24', __( 'Pilihan warna di 4 material', 'blackroll' ) ),
	array( __( 'Ready', 'blackroll' ), __( 'Stock siap — konsultasi hingga pemasangan cepat', 'blackroll' ) ),
	array( '5', __( 'Lapis blackout: tahan air, minyak, anti-jamur', 'blackroll' ) ),
);
?>
<!-- wp:group {"align":"full","tagName":"section","className":"blackroll-trust","backgroundColor":"stone","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull blackroll-trust has-stone-background-color has-background">
	<!-- wp:html -->
	<ul class="blackroll-stats alignwide">
		<?php foreach ( $blackroll_stats as $stat ) : ?>
			<li class="blackroll-stat blackroll-reveal">
				<span class="blackroll-stat__value"><?php echo esc_html( $stat[0] ); ?></span>
				<span class="blackroll-stat__label"><?php echo esc_html( $stat[1] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
