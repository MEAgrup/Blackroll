<?php
/**
 * Blackroll seed command (PATCH-14). Runs only under WP-CLI:
 *   wp blackroll seed
 *
 * Creates realistic sample content for QA/dev: core pages (ID slugs), a few
 * shades per Series, a few projects (one featured), and 3 seed blog stubs.
 * Idempotent-ish: skips items that already exist by slug/title.
 *
 * Mapped into wp-content/mu-plugins by .wp-env.json but inert outside WP-CLI.
 *
 * @package BlackrollCore
 */

if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

/**
 * Seed command.
 */
class Blackroll_Seed_Command {

	/**
	 * Seed sample content.
	 *
	 * ## EXAMPLES
	 *   wp blackroll seed
	 */
	private $media_cache = array();

	public function __invoke() {
		$this->pages();
		$this->shades();
		$this->projects();
		$this->articles();
		WP_CLI::success( 'Blackroll content seeded (real SKU list + photos).' );
	}

	/**
	 * Sideload a bundled seed-asset image into the media library once (cached).
	 *
	 * @param string $rel Path relative to blackroll-core/seed-assets (e.g. shades/ml001a.webp).
	 * @param string $alt Alt text (Bahasa Indonesia).
	 * @return int Attachment ID or 0.
	 */
	private function media( $rel, $alt = '' ) {
		if ( isset( $this->media_cache[ $rel ] ) ) {
			return $this->media_cache[ $rel ];
		}
		$path = defined( 'BLACKROLL_CORE_DIR' ) ? BLACKROLL_CORE_DIR . 'seed-assets/' . $rel : '';
		if ( ! $path || ! file_exists( $path ) ) {
			return 0;
		}
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$tmp = wp_tempnam( basename( $path ) );
		copy( $path, $tmp );
		$id = media_handle_sideload( array( 'name' => basename( $path ), 'tmp_name' => $tmp ), 0 );
		if ( is_wp_error( $id ) ) {
			@unlink( $tmp );
			return 0;
		}
		if ( $alt ) {
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		}
		$this->media_cache[ $rel ] = $id;
		return $id;
	}

	private function post_exists_by_title( $title, $type ) {
		$found = get_posts(
			array(
				'post_type'      => $type,
				'title'          => $title,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		return ! empty( $found );
	}

	private function ensure_page( $title, $slug, $template = '', $content = '' ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			return $existing->ID;
		}
		if ( '' === $content ) {
			$content = '<!-- wp:paragraph --><p>' . esc_html( $title ) . ' (konten contoh).</p><!-- /wp:paragraph -->';
		}
		$id = wp_insert_post(
			array(
				'post_title'  => $title,
				'post_name'   => $slug,
				'post_status' => 'publish',
				'post_type'   => 'page',
				'post_content' => $content,
			)
		);
		if ( $template && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $template );
		}
		WP_CLI::log( "page: {$slug}" );
		return $id;
	}

	private function pages() {
		$this->ensure_page( 'Tentang Kami', 'tentang-kami' );
		$this->ensure_page( 'Blinds Manual', 'blinds-manual', 'template-product' );
		$this->ensure_page( 'Blinds Motorized', 'blinds-motorized', 'template-product' );
		$this->ensure_page( 'Material & Warna', 'material-warna', '', '<!-- wp:pattern {"slug":"blackroll/page-material-color"} /-->' );
		$this->ensure_page( 'Portofolio', 'portofolio' );
		$this->ensure_page( 'Kontak', 'kontak', '', '<!-- wp:pattern {"slug":"blackroll/page-contact"} /-->' );
		$this->ensure_page( 'Terima Kasih', 'terima-kasih', 'template-thankyou', '<!-- wp:pattern {"slug":"blackroll/contact-thankyou"} /-->' );
		$this->ensure_page( 'Pengiriman Gagal', 'gagal', 'template-thankyou', '<!-- wp:pattern {"slug":"blackroll/contact-failed"} /-->' );
		$this->ensure_page( 'Kebijakan Privasi', 'kebijakan-privasi' );

		// Set the homepage to a static front page if one is named "Beranda".
		$home = $this->ensure_page( 'Beranda', 'beranda' );
		if ( $home ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home );
		}
	}

	private function shades() {
		// Authoritative list from the client SKU sheet (2026-07-13).
		// [ display, sku, series, material_type, photo-slug|'' ]
		// Series: black = RBXL (accessory Hitam), white = RBPL (accessory Putih).
		$shades = array(
			// White Series — Blackout (with product photos).
			array( 'ML003C', 'ML003C', 'white-series', 'blackout', 'ml003c' ),
			array( 'ML004D', 'ML004D', 'white-series', 'blackout', 'ml004d' ),
			array( 'ML005E', 'ML005E', 'white-series', 'blackout', 'ml005e' ),
			array( 'ML006F', 'ML006F', 'white-series', 'blackout', 'ml006f' ),
			array( 'ML007G', 'ML007G', 'white-series', 'blackout', 'ml007g' ),
			array( 'ML002B', 'ML002B', 'white-series', 'blackout', 'ml002b' ),
			// Black Series — Blackout.
			array( 'ML001A', 'ML001A', 'black-series', 'blackout', 'ml001a' ),
			array( 'WD003C', 'WD003C', 'black-series', 'blackout', '' ),
			array( 'LD002B', 'LD002B', 'black-series', 'blackout', '' ),
			array( 'SV003C', 'SV003C', 'black-series', 'blackout', '' ),
			array( 'HG001A', 'HG001A', 'black-series', 'blackout', '' ),
			array( 'H003', 'H003', 'black-series', 'blackout', '' ),
			array( 'K004', 'K004', 'black-series', 'blackout', '' ),
			array( 'L003', 'L003', 'black-series', 'blackout', '' ),
			// Black Series — Solar Screen.
			array( 'A004', 'A004', 'black-series', 'solar_screen', '' ),
			array( 'B001', 'B001', 'black-series', 'solar_screen', '' ),
			array( 'C003', 'C003', 'black-series', 'solar_screen', '' ),
			array( 'D003', 'D003', 'black-series', 'solar_screen', 'd001a' ),
			array( 'E003', 'E003', 'black-series', 'solar_screen', '' ),
			array( 'F001', 'F001', 'black-series', 'solar_screen', '' ),
			// Black Series — Dimout & Zebra (catalogue extras; not in the 2-material selector).
			array( 'M001', 'M001', 'black-series', 'dimout', 'd002' ),
			array( 'W001', 'W001', 'black-series', 'zebra', '' ),
			array( 'S004', 'S004', 'black-series', 'zebra', '' ),
			array( 'V001', 'V001', 'black-series', 'zebra', '' ),
		);
		foreach ( $shades as $s ) {
			list( $name, $sku, $series, $material, $photo ) = $s;
			if ( $this->post_exists_by_title( $name, 'shade' ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_title'  => $name,
					'post_status' => 'publish',
					'post_type'   => 'shade',
				)
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, 'sku_code', $sku );
			update_post_meta( $id, 'material_type', $material );
			update_post_meta( $id, 'material_blackout', 'blackout' === $material ? 1 : 0 );
			update_post_meta( $id, 'material_solar', 'solar_screen' === $material ? 1 : 0 );
			wp_set_object_terms( $id, $series, 'color_series' );
			if ( $photo ) {
				$att = $this->media( "shades/{$photo}.webp", "Roller blinds Blackroll {$sku}" );
				if ( $att ) {
					set_post_thumbnail( $id, $att );
					update_post_meta( $id, 'preview_image', $att );
				}
			}
			WP_CLI::log( "shade: {$name} ({$sku}, {$material})" );
		}
	}

	private function projects() {
		$projects = array(
			array( 'Apartemen — Ruang Tamu', 'apartment', true, 'ruang-tamu' ),
			array( 'Kantor — Ruang Kerja', 'office', true, 'kantor' ),
			array( 'Rumah — Dapur', 'residential', true, 'ruang-dapur' ),
			array( 'Home Office', 'office', false, 'ruang-office' ),
		);
		foreach ( $projects as $p ) {
			list( $title, $type, $featured, $photo ) = $p;
			if ( $this->post_exists_by_title( $title, 'project' ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_title'  => $title,
					'post_status' => 'publish',
					'post_type'   => 'project',
				)
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, 'featured', $featured ? 1 : 0 );
			update_post_meta( $id, 'location', 'Indonesia' );
			wp_set_object_terms( $id, $type, 'project_type' );
			$att = $this->media( "lifestyle/{$photo}.webp", "Instalasi roller blinds Blackroll — {$title}" );
			if ( $att ) {
				set_post_thumbnail( $id, $att );
			}
			WP_CLI::log( "project: {$title}" );
		}
	}

	private function articles() {
		$this->ensure_category();
		$articles = array(
			array( 'Panduan Pemasangan Roller Blinds: Dinding vs Depan Kusen', 'panduan', 'panduan-pasang-1' ),
			array( 'Kenapa Blackout Blackroll Menahan Cahaya 100%: 5 Lapisan Teknologi', 'produk', 'blackout-5-lapis' ),
			array( 'Tahan Air, Tahan Minyak, Anti Jamur: Roller Blinds untuk Dapur', 'produk', 'tahan-air-minyak' ),
		);
		foreach ( $articles as $a ) {
			list( $title, $cat, $photo ) = $a;
			if ( $this->post_exists_by_title( $title, 'post' ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_title'  => $title,
					'post_status' => 'draft',
					'post_type'   => 'post',
					'post_content' => '<!-- wp:paragraph --><p>Draft — konten ditulis tim konten.</p><!-- /wp:paragraph -->',
				)
			);
			if ( ! is_wp_error( $id ) ) {
				wp_set_post_categories( $id, array( get_cat_ID( ucfirst( $cat ) ) ) );
				$att = $this->media( "tech/{$photo}.webp", $title );
				if ( $att ) {
					set_post_thumbnail( $id, $att );
				}
				WP_CLI::log( "article: {$title}" );
			}
		}
	}

	private function ensure_category() {
		foreach ( array( 'Panduan', 'Inspirasi', 'Produk' ) as $name ) {
			if ( ! get_cat_ID( $name ) ) {
				wp_insert_term( $name, 'category' );
			}
		}
	}
}

WP_CLI::add_command( 'blackroll seed', 'Blackroll_Seed_Command' );
