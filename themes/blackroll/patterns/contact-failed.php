<?php
/**
 * Title: Contact — Submit Failed
 * Slug: blackroll/contact-failed
 * Categories: blackroll-general
 * Description: Failure state after a Sebari submit. Set this URL as the failure redirect in Sebari.
 * Inserter: no
 *
 * @package Blackroll
 */
?>
<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-text-align-center has-xx-large-font-size"><?php esc_html_e( 'Pengiriman Gagal', 'blackroll' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php esc_html_e( 'Maaf, pesan Anda belum terkirim. Silakan coba lagi, atau hubungi kami langsung via WhatsApp di bawah ini.', 'blackroll' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
	<!-- wp:shortcode -->[blackroll_fallback_contact]<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
	<!-- wp:button {"backgroundColor":"white","textColor":"black"} -->
	<div class="wp-block-button"><a class="wp-block-button__link has-black-color has-white-background-color has-text-color has-background wp-element-button" href="/kontak/"><?php esc_html_e( 'Coba Lagi', 'blackroll' ); ?></a></div>
	<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
