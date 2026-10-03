<?php
/**
 * Title: Home — Hero
 * Slug: blackroll/home-hero
 * Categories: blackroll-home
 * Description: Typography-led hero. H1 is the LCP element (real HTML, not baked into motion).
 * The optional background video (client decision 2026-09, premium-over-score
 * trade-off — deploy/QA-CHECKLIST.md) loads after the H1 and never delays it;
 * see blackroll_hero_video_url() in plugins/blackroll-core/inc/config.php.
 *
 * @package Blackroll
 */
$blackroll_hero_video = function_exists( 'blackroll_hero_video_url' ) ? blackroll_hero_video_url() : '';
?>
<!-- wp:group {"align":"full","tagName":"section","className":"blackroll-hero blackroll-dark","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"black","textColor":"white","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull blackroll-hero blackroll-dark has-white-color has-black-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"level":1,"fontSize":"display"} -->
	<h1 class="wp-block-heading has-display-font-size"><?php esc_html_e( 'Blinds Premium Terjangkau untuk Hunian Modern', 'blackroll' ); ?></h1>
	<!-- /wp:heading -->

	<?php if ( $blackroll_hero_video ) : ?>
	<!-- wp:html -->
	<div class="blackroll-hero__media" aria-hidden="true">
		<video
			class="blackroll-hero__video"
			data-blackroll-hero-video
			data-src="<?php echo esc_url( $blackroll_hero_video ); ?>"
			poster="<?php echo esc_url( blackroll_hero_video_poster() ); ?>"
			muted
			loop
			playsinline
			preload="none"
		></video>
	</div>
	<!-- /wp:html -->
	<?php endif; ?>

	<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.2rem"}},"textColor":"grey"} -->
	<p class="has-grey-color has-text-color" style="font-size:1.2rem"><?php esc_html_e( 'Roller blinds monokrom, tahan air & minyak, dengan pilihan Manual dan Motorized. Salah satu pelopor roller blinds premium di Indonesia.', 'blackroll' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"white","textColor":"black"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-black-color has-white-background-color has-text-color has-background wp-element-button" href="/kontak/"><?php esc_html_e( 'Hubungi Kami', 'blackroll' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->
