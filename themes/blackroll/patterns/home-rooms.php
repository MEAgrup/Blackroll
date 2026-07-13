<?php
/**
 * Title: Home — Room Showcase
 * Slug: blackroll/home-rooms
 * Categories: blackroll-home
 * Description: "Desain Timeless untuk Berbagai Ruangan" — real lifestyle photos (office/living/kitchen).
 *
 * @package Blackroll
 */
$uri = get_template_directory_uri() . '/assets/images/rooms/';
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
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $uri . 'kantor.webp' ); ?>" alt="<?php esc_attr_e( 'Roller blinds Blackroll di ruang kerja', 'blackroll' ); ?>" style="aspect-ratio:3/4;object-fit:cover" width="600" height="800" loading="lazy" decoding="async"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $uri . 'ruang-tamu.webp' ); ?>" alt="<?php esc_attr_e( 'Roller blinds Blackroll di ruang tamu', 'blackroll' ); ?>" style="aspect-ratio:3/4;object-fit:cover" width="600" height="800" loading="lazy" decoding="async"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $uri . 'ruang-dapur.webp' ); ?>" alt="<?php esc_attr_e( 'Roller blinds Blackroll di dapur', 'blackroll' ); ?>" style="aspect-ratio:3/4;object-fit:cover" width="600" height="800" loading="lazy" decoding="async"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
