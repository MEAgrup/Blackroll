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
	public function __invoke() {
		$this->pages();
		$this->shades();
		$this->projects();
		$this->articles();
		WP_CLI::success( 'Blackroll sample content seeded.' );
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

	private function ensure_page( $title, $slug, $template = '' ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			return $existing->ID;
		}
		$id = wp_insert_post(
			array(
				'post_title'  => $title,
				'post_name'   => $slug,
				'post_status' => 'publish',
				'post_type'   => 'page',
				'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $title ) . ' (konten contoh).</p><!-- /wp:paragraph -->',
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
		$this->ensure_page( 'Material & Warna', 'material-warna' );
		$this->ensure_page( 'Portofolio', 'portofolio' );
		$this->ensure_page( 'Kontak', 'kontak' );
		$this->ensure_page( 'Terima Kasih', 'terima-kasih', 'template-thankyou' );
		$this->ensure_page( 'Kebijakan Privasi', 'kebijakan-privasi' );

		// Set the homepage to a static front page if one is named "Beranda".
		$home = $this->ensure_page( 'Beranda', 'beranda' );
		if ( $home ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home );
		}
	}

	private function shades() {
		$shades = array(
			array( 'Cream', 'white-series', 'ML002B', true, true ),
			array( 'Light Grey', 'white-series', 'ML003A', true, false ),
			array( 'Charcoal', 'black-series', 'ML001A', true, true ),
			array( 'Grey', 'black-series', 'D001A', false, true ),
		);
		foreach ( $shades as $s ) {
			list( $name, $series, $sku, $blackout, $solar ) = $s;
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
			update_post_meta( $id, 'material_blackout', $blackout ? 1 : 0 );
			update_post_meta( $id, 'material_solar', $solar ? 1 : 0 );
			wp_set_object_terms( $id, $series, 'color_series' );
			WP_CLI::log( "shade: {$name} ({$sku})" );
		}
	}

	private function projects() {
		$projects = array(
			array( 'Apartemen Menteng — Ruang Tamu', 'apartment', true ),
			array( 'Kantor BSD — Meeting Room', 'office', true ),
			array( 'Rumah Bandung — Kamar Utama', 'residential', false ),
		);
		foreach ( $projects as $p ) {
			list( $title, $type, $featured ) = $p;
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
			WP_CLI::log( "project: {$title}" );
		}
	}

	private function articles() {
		$this->ensure_category();
		$articles = array(
			array( 'Panduan Pemasangan Roller Blinds: Dinding vs Depan Kusen', 'panduan' ),
			array( 'Kenapa Blackout Blackroll Menahan Cahaya 100%: 5 Lapisan Teknologi', 'produk' ),
			array( 'Tahan Air, Tahan Minyak, Anti Jamur: Roller Blinds untuk Dapur', 'produk' ),
		);
		foreach ( $articles as $a ) {
			list( $title, $cat ) = $a;
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
