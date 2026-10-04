<?php
/**
 * Title: Home — Collection
 * Slug: blackroll/home-collection
 * Categories: blackroll-home
 * Description: Roller blind colours (main line) as a large photo grid, plus Zebra as the
 * secondary line. Photos: Sept 2026 client shoot (assets/images/collection/).
 *
 * @package Blackroll
 */
$blackroll_img    = get_template_directory_uri() . '/assets/images/collection/';
$blackroll_roller = array(
	array( 'charcoal-linen', __( 'Charcoal Linen', 'blackroll' ), __( 'Blackout', 'blackroll' ) ),
	array( 'taupe', __( 'Taupe', 'blackroll' ), __( 'Blackout', 'blackroll' ) ),
	array( 'beige', __( 'Beige', 'blackroll' ), __( 'Blackout', 'blackroll' ) ),
	array( 'sky', __( 'Sky Blue', 'blackroll' ), __( 'Solar Screen', 'blackroll' ) ),
	array( 'grey', __( 'Grey', 'blackroll' ), __( 'Blackout', 'blackroll' ) ),
	array( 'mist-solar', __( 'Mist', 'blackroll' ), __( 'Solar Screen', 'blackroll' ) ),
);
?>
<!-- wp:group {"align":"full","tagName":"section","className":"blackroll-collection","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull blackroll-collection">
	<!-- wp:html -->
	<div class="blackroll-section-head alignwide">
		<p class="blackroll-eyebrow"><?php esc_html_e( 'Koleksi', 'blackroll' ); ?></p>
		<h2 class="has-xx-large-font-size"><?php esc_html_e( 'Roller Blind,', 'blackroll' ); ?> <em><?php esc_html_e( 'warna untuk setiap ruang', 'blackroll' ); ?></em></h2>
		<p class="blackroll-section-head__lead"><?php esc_html_e( 'Black Series & White Series dalam Blackout, Solar Screen, dan Dimout. Ukuran lebar 60, 80, 100, dan 120 cm.', 'blackroll' ); ?></p>
	</div>

	<ul class="blackroll-collection__grid alignwide">
		<?php foreach ( $blackroll_roller as $c ) : ?>
			<li class="blackroll-card blackroll-reveal">
				<a href="<?php echo esc_url( home_url( '/material-warna/' ) ); ?>">
					<span class="blackroll-card__media">
						<img src="<?php echo esc_url( $blackroll_img . $c[0] . '-front.webp' ); ?>" alt="<?php echo esc_attr( sprintf( /* translators: %s colour name */ __( 'Roller blind Blackroll warna %s', 'blackroll' ), $c[1] ) ); ?>" width="1284" height="1600" loading="lazy" decoding="async">
					</span>
					<span class="blackroll-card__meta">
						<span class="blackroll-card__title"><?php echo esc_html( $c[1] ); ?></span>
						<span class="blackroll-card__tag"><?php echo esc_html( $c[2] ); ?></span>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="blackroll-zebra alignwide blackroll-reveal">
		<div class="blackroll-zebra__media">
			<img src="<?php echo esc_url( $blackroll_img . 'zebra-texture.webp' ); ?>" alt="<?php esc_attr_e( 'Detail kain zebra blind Blackroll', 'blackroll' ); ?>" width="1067" height="1600" loading="lazy" decoding="async">
		</div>
		<div class="blackroll-zebra__copy">
			<p class="blackroll-eyebrow"><?php esc_html_e( 'Juga tersedia', 'blackroll' ); ?></p>
			<h3 class="has-x-large-font-size"><?php esc_html_e( 'Zebra Blind', 'blackroll' ); ?></h3>
			<p><?php esc_html_e( 'Dua lapis kain bergaris — solid dan sheer — yang bisa digeser untuk mengatur cahaya tanpa menggulung penuh.', 'blackroll' ); ?></p>
			<p><a class="blackroll-link" href="<?php echo esc_url( home_url( '/material-warna/' ) ); ?>"><?php esc_html_e( 'Lihat warna Zebra', 'blackroll' ); ?></a></p>
		</div>
	</div>

	<p class="alignwide blackroll-collection__more">
		<a class="wp-element-button" href="<?php echo esc_url( home_url( '/material-warna/' ) ); ?>"><?php esc_html_e( 'Semua Material & Warna', 'blackroll' ); ?></a>
	</p>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
