<?php
/**
 * Portfolio (Module 7) — server-rendered filterable grid from the project CPT.
 *
 * Shortcode: [blackroll_portfolio]
 *
 * Filter bar (All + project_type terms), lazy grid with fixed aspect-ratio
 * cards (CLS-safe), first N shown then "Muat Lebih Banyak", and a single
 * accessible lightbox. portfolio.js enhances filtering + Load More + lightbox.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode(
	'blackroll_portfolio',
	function () {
		$initial = 9; // First render count (Module 7 — then Load More).

		$terms = get_terms(
			array(
				'taxonomy'   => 'project_type',
				'hide_empty' => true,
			)
		);
		$projects = get_posts(
			array(
				'post_type'      => 'project',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		if ( empty( $projects ) ) {
			return '<p>' . esc_html__( 'Portofolio akan segera ditampilkan.', 'blackroll-core' ) . '</p>';
		}

		ob_start();
		?>
		<div data-blackroll-portfolio>
			<div class="blackroll-filter" role="group" aria-label="<?php esc_attr_e( 'Filter portofolio', 'blackroll-core' ); ?>">
				<button type="button" class="blackroll-filter__btn" data-filter="all" aria-pressed="true"><?php esc_html_e( 'Semua', 'blackroll-core' ); ?></button>
				<?php if ( ! is_wp_error( $terms ) ) : foreach ( $terms as $t ) : ?>
					<button type="button" class="blackroll-filter__btn" data-filter="<?php echo esc_attr( $t->slug ); ?>" aria-pressed="false"><?php echo esc_html( $t->name ); ?></button>
				<?php endforeach; endif; ?>
			</div>

			<div class="blackroll-grid">
				<?php
				$i = 0;
				foreach ( $projects as $p ) :
					$i++;
					$type_terms = wp_get_object_terms( $p->ID, 'project_type', array( 'fields' => 'slugs' ) );
					$type       = is_wp_error( $type_terms ) || empty( $type_terms ) ? '' : $type_terms[0];
					$thumb_id   = get_post_thumbnail_id( $p->ID );
					$card       = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'blackroll-card' ) : '';
					$full       = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
					$alt        = $thumb_id ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : $p->post_title;
					$deferred   = $i > $initial;
					?>
					<div class="blackroll-grid__item" data-type="<?php echo esc_attr( $type ); ?>" <?php echo $deferred ? 'data-deferred hidden' : ''; ?>>
						<a href="<?php echo esc_url( $full ); ?>" data-lightbox="<?php echo esc_url( $full ); ?>" data-alt="<?php echo esc_attr( $alt ); ?>" aria-label="<?php echo esc_attr( $p->post_title ); ?>">
							<?php if ( $card ) : ?>
								<img src="<?php echo esc_url( $card ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="640" height="480" loading="lazy" decoding="async">
							<?php endif; ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $projects ) > $initial ) : ?>
				<p class="blackroll-loadmore">
					<button type="button" class="wp-element-button" data-load-more><?php esc_html_e( 'Muat Lebih Banyak', 'blackroll-core' ); ?></button>
				</p>
			<?php endif; ?>
		</div>

		<div class="blackroll-lightbox" aria-modal="true" role="dialog" aria-label="<?php esc_attr_e( 'Pratinjau foto', 'blackroll-core' ); ?>">
			<button type="button" class="blackroll-lightbox__close" aria-label="<?php esc_attr_e( 'Tutup', 'blackroll-core' ); ?>">&times;</button>
			<?php // No src until a photo is opened: src="" makes the browser fetch the page itself as an image. ?>
			<img class="blackroll-lightbox__img" alt="">
		</div>
		<?php
		return ob_get_clean();
	}
);
