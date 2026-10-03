<?php
/**
 * Title: Home — Portfolio Highlights
 * Slug: blackroll/home-portfolio-highlights
 * Categories: blackroll-home
 * Description: Curated featured projects (project CPT). "featured" filtering refined in Step 8.
 *
 * @package Blackroll
 */
?>
<!-- wp:group {"tagName":"section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"fontSize":"xx-large"} -->
	<h2 class="wp-block-heading has-xx-large-font-size"><?php esc_html_e( 'Portofolio Pilihan', 'blackroll' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:query {"queryId":0,"query":{"perPage":3,"postType":"project","offset":0,"order":"desc","orderBy":"date","inherit":false,"blackrollFeatured":true},"align":"wide"} -->
	<div class="wp-block-query alignwide">
		<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
			<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->
			<!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->
		<!-- /wp:post-template -->
		<!-- wp:query-no-results -->
			<!-- wp:paragraph {"textColor":"grey-text"} -->
			<p class="has-grey-text-color has-text-color"><?php esc_html_e( 'Portofolio akan segera ditampilkan.', 'blackroll' ); ?></p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:query -->

	<!-- wp:paragraph -->
	<p><a href="/portofolio/"><?php esc_html_e( 'Lihat Semua Portofolio', 'blackroll' ); ?></a></p>
	<!-- /wp:paragraph -->
</section>
<!-- /wp:group -->
