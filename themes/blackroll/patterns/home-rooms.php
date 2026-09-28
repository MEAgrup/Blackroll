<?php
/**
 * Title: Home — Room Showcase
 * Slug: blackroll/home-rooms
 * Categories: blackroll-home
 * Description: "Desain Timeless untuk Berbagai Ruangan" — three colours from the Sept 2026 client shoot.
 * The earlier room images were marketing posters (baked-in text), not photos; swap in real
 * room/lifestyle shots here once the client supplies them.
 *
 * @package Blackroll
 */
$uri = get_template_directory_uri() . '/assets/images/collection/';
?>
<!-- wp:group {"tagName":"section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","fontSize":"xx-large"} -->
	<h2 class="wp-block-heading has-text-align-center has-xx-large-font-size"><?php esc_html_e( 'Desain Timeless untuk Berbagai Ruangan', 'blackroll' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $uri . 'charcoal-linen-front.webp' ); ?>" alt="<?php esc_attr_e( 'Roller blind Blackroll warna charcoal linen', 'blackroll' ); ?>" style="aspect-ratio:3/4;object-fit:cover" width="1285" height="1600" loading="lazy" decoding="async"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $uri . 'beige-front.webp' ); ?>" alt="<?php esc_attr_e( 'Roller blind Blackroll warna beige', 'blackroll' ); ?>" style="aspect-ratio:3/4;object-fit:cover" width="1285" height="1600" loading="lazy" decoding="async"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $uri . 'zebra-ivory-front.webp' ); ?>" alt="<?php esc_attr_e( 'Zebra blind Blackroll warna ivory', 'blackroll' ); ?>" style="aspect-ratio:3/4;object-fit:cover" width="1285" height="1600" loading="lazy" decoding="async"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
