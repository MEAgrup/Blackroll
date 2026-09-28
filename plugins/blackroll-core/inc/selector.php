<?php
/**
 * Material & Color selector (Module 6) — server-rendered from the shade CPT.
 *
 * Shortcode: [blackroll_selector]
 *
 * Renders real HTML controls (SEO / progressive enhancement): Material →
 * Color Series → Shade → Size, plus a preview area and contextual label.
 * selector.js enhances it (filter shades by series+material, swap preview,
 * sync aria-pressed). No URL params, terminal CTA → Contact (Module 6 Rule 4).
 *
 * Materials shown = Blackout + Solar Screen (locked Module 6). Dimout/Zebra
 * remain in the data via material_type; add them to $materials to expand.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Materials exposed in the selector (filterable — expand to 4 if scope allows).
 *
 * @return array<string,string> value => label
 */
function blackroll_selector_materials() {
	return apply_filters(
		'blackroll_selector_materials',
		array(
			'blackout'     => __( 'Blackout', 'blackroll-core' ),
			'solar_screen' => __( 'Solar Screen', 'blackroll-core' ),
			'dimout'       => __( 'Dimout', 'blackroll-core' ),
			'zebra'        => __( 'Zebra Blinds', 'blackroll-core' ),
		)
	);
}

/**
 * Resolve a usable image URL for a shade (swatch/preview fallback chain).
 *
 * @param int    $post_id Shade ID.
 * @param string $meta    Meta key to try first.
 * @param string $size    Image size.
 * @return string
 */
function blackroll_shade_image( $post_id, $meta, $size = 'blackroll-preview' ) {
	$att = (int) get_post_meta( $post_id, $meta, true );
	if ( ! $att ) {
		$att = get_post_thumbnail_id( $post_id );
	}
	$src = $att ? wp_get_attachment_image_url( $att, $size ) : '';
	if ( $src ) {
		return $src;
	}
	return blackroll_shade_theme_image( get_post_meta( $post_id, 'sku_code', true ), 'swatch_image' === $meta ? 'swatch' : 'front' );
}

/**
 * SKU → curated photo key in the theme (themes/blackroll/assets/images/collection/).
 * Only SKUs confirmed from the client's folder names are listed; add the rest
 * once the client maps the "blind N" folders to SKUs (see the README there).
 *
 * @return array<string,string>
 */
function blackroll_shade_photo_keys() {
	return apply_filters(
		'blackroll_shade_photo_keys',
		array(
			'A004' => 'a004',       // Folder "Roller blindes RBXL A004".
			'S004' => 'zebra-s004', // Folder "XLS004".
		)
	);
}

/**
 * Theme-shipped photo for a SKU, used when the shade has no Media Library image.
 *
 * @param string $sku  SKU code.
 * @param string $kind 'swatch' or 'front'.
 * @return string URL or ''.
 */
function blackroll_shade_theme_image( $sku, $kind ) {
	$keys = blackroll_shade_photo_keys();
	if ( ! $sku || empty( $keys[ $sku ] ) ) {
		return '';
	}
	$rel = 'assets/images/collection/' . $keys[ $sku ] . '-' . $kind . '.webp';
	return is_readable( get_theme_file_path( $rel ) ) ? get_theme_file_uri( $rel ) : '';
}

add_shortcode(
	'blackroll_selector',
	function () {
		$materials = blackroll_selector_materials();
		$sizes     = array( '60', '80', '100', '120' );

		// Series terms that actually have shades.
		$series_terms = get_terms(
			array(
				'taxonomy'   => 'color_series',
				'hide_empty' => true,
			)
		);
		if ( is_wp_error( $series_terms ) || empty( $series_terms ) ) {
			return '<p>' . esc_html__( 'Data warna belum tersedia.', 'blackroll-core' ) . '</p>';
		}

		$shades = get_posts(
			array(
				'post_type'      => 'shade',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		ob_start();
		?>
		<?php $first_preview = ! empty( $shades ) ? blackroll_shade_image( $shades[0]->ID, 'preview_image', 'blackroll-preview' ) : ''; ?>
		<div class="blackroll-selector" data-blackroll-selector data-preview-generic="<?php echo esc_url( $first_preview ); ?>">
			<div class="blackroll-selector__controls">

				<div class="blackroll-option-block">
					<p class="blackroll-option-block__label"><?php esc_html_e( 'Material', 'blackroll-core' ); ?></p>
					<div class="blackroll-option-group" role="group" aria-label="<?php esc_attr_e( 'Material', 'blackroll-core' ); ?>">
						<?php $first = true; foreach ( $materials as $val => $label ) : ?>
							<button type="button" class="blackroll-option" data-select="material" data-value="<?php echo esc_attr( $val ); ?>" aria-pressed="<?php echo $first ? 'true' : 'false'; ?>"><?php echo esc_html( $label ); ?></button>
						<?php $first = false; endforeach; ?>
					</div>
				</div>

				<div class="blackroll-option-block">
					<p class="blackroll-option-block__label"><?php esc_html_e( 'Seri Warna', 'blackroll-core' ); ?></p>
					<div class="blackroll-option-group" role="group" aria-label="<?php esc_attr_e( 'Seri Warna', 'blackroll-core' ); ?>">
						<?php $first = true; foreach ( $series_terms as $term ) : ?>
							<button type="button" class="blackroll-option" data-select="series" data-value="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="<?php echo $first ? 'true' : 'false'; ?>"><?php echo esc_html( $term->name ); ?></button>
						<?php $first = false; endforeach; ?>
					</div>
				</div>

				<div class="blackroll-option-block">
					<p class="blackroll-option-block__label"><?php esc_html_e( 'Warna', 'blackroll-core' ); ?></p>
					<div class="blackroll-option-group blackroll-swatches" role="group" aria-label="<?php esc_attr_e( 'Warna', 'blackroll-core' ); ?>">
						<?php
						foreach ( $shades as $shade ) :
							$sid      = $shade->ID;
							$sku      = get_post_meta( $sid, 'sku_code', true );
							$mtype    = get_post_meta( $sid, 'material_type', true );
							$s_terms  = wp_get_object_terms( $sid, 'color_series', array( 'fields' => 'slugs' ) );
							$series   = is_wp_error( $s_terms ) || empty( $s_terms ) ? '' : $s_terms[0];
							// Only render shades whose material is in the exposed set.
							if ( ! array_key_exists( $mtype, $materials ) ) {
								continue;
							}
							$swatch   = blackroll_shade_image( $sid, 'swatch_image', 'blackroll-swatch' );
							$preview  = blackroll_shade_image( $sid, 'preview_image', 'blackroll-preview' );
							$pv_black = blackroll_shade_image( $sid, 'preview_blackout', 'blackroll-preview' );
							$pv_solar = blackroll_shade_image( $sid, 'preview_solar', 'blackroll-preview' );
							$style    = $swatch ? ' style="background-image:url(' . esc_url( $swatch ) . ')"' : '';
							$classes  = 'blackroll-swatch' . ( $swatch ? '' : ' blackroll-swatch--code' );
							?>
							<button type="button" class="<?php echo esc_attr( $classes ); ?>" data-select="shade" aria-pressed="false"
								data-value="<?php echo esc_attr( $shade->post_title ); ?>"
								data-series="<?php echo esc_attr( $series ); ?>"
								data-materials="<?php echo esc_attr( $mtype ); ?>"
								data-sku="<?php echo esc_attr( $sku ); ?>"
								<?php if ( $preview ) : ?>data-preview="<?php echo esc_url( $preview ); ?>"<?php endif; ?>
								<?php if ( $pv_black ) : ?>data-preview-blackout="<?php echo esc_url( $pv_black ); ?>"<?php endif; ?>
								<?php if ( $pv_solar ) : ?>data-preview-solar_screen="<?php echo esc_url( $pv_solar ); ?>"<?php endif; ?>
								aria-label="<?php echo esc_attr( $shade->post_title . ' ' . $sku ); ?>"
								title="<?php echo esc_attr( $shade->post_title . ' · ' . $sku ); ?>"<?php echo $style; // phpcs:ignore ?>>
								<?php if ( $swatch ) : ?>
									<span class="screen-reader-text"><?php echo esc_html( $shade->post_title ); ?></span>
								<?php else : ?>
									<?php // No swatch photo yet: show the SKU instead of a blank tile. ?>
									<span class="blackroll-swatch__code"><?php echo esc_html( $sku ? $sku : $shade->post_title ); ?></span>
								<?php endif; ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="blackroll-option-block">
					<p class="blackroll-option-block__label"><?php esc_html_e( 'Ukuran (cm)', 'blackroll-core' ); ?></p>
					<div class="blackroll-option-group" role="group" aria-label="<?php esc_attr_e( 'Ukuran', 'blackroll-core' ); ?>">
						<?php foreach ( $sizes as $sz ) : ?>
							<button type="button" class="blackroll-option" data-select="size" data-value="<?php echo esc_attr( $sz ); ?>" aria-pressed="false"><?php echo esc_html( $sz ); ?></button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="blackroll-selector__preview">
				<?php // An empty src makes the browser re-request the page itself as an image; keep the element for the JS swap but leave it hidden until it has a real source. ?>
				<img<?php echo $first_preview ? ' src="' . esc_url( $first_preview ) . '"' : ' hidden'; ?> alt="<?php esc_attr_e( 'Pratinjau roller blinds Blackroll', 'blackroll-core' ); ?>" width="900" height="900" loading="lazy" decoding="async">
				<p class="blackroll-selector__empty"<?php echo $first_preview ? ' hidden' : ''; ?>><?php esc_html_e( 'Foto warna ini segera hadir — tanyakan contoh kainnya via WhatsApp.', 'blackroll-core' ); ?></p>
				<p class="blackroll-selector__label" aria-live="polite"></p>
			</div>

			<p class="blackroll-selector__cta">
				<a class="wp-element-button" href="<?php echo esc_url( home_url( '/kontak/' ) ); ?>"><?php esc_html_e( 'Hubungi Kami', 'blackroll-core' ); ?></a>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}
);
