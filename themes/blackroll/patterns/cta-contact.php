<?php
/**
 * Title: CTA — Contact
 * Slug: blackroll/cta-contact
 * Categories: blackroll-cta
 * Description: Closing call-to-action band routing to the Contact page.
 *
 * @package Blackroll
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"black","textColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-white-color has-black-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-text-align-center has-x-large-font-size"><?php esc_html_e( 'Tertarik? Hubungi Kami', 'blackroll' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center","textColor":"grey"} -->
	<p class="has-text-align-center has-grey-color has-text-color"><?php esc_html_e( 'Konsultasi gratis, respon cepat via WhatsApp.', 'blackroll' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"white","textColor":"black"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-black-color has-white-background-color has-text-color has-background wp-element-button" href="/kontak/"><?php esc_html_e( 'Hubungi Kami', 'blackroll' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
