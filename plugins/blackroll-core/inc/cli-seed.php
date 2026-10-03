<?php
/**
 * WP-CLI content commands.
 *
 *   wp blackroll seed              Structure + data: core pages (ID slugs, correct
 *                                 templates/patterns, /produk/ nesting, blog posts
 *                                 page), the 24-SKU shade list, projects, articles,
 *                                 and sideloads the bundled seed-asset photos.
 *   wp blackroll content           Editorial pass: (re)writes the real Bahasa
 *                                 Indonesia copy from inc/seed-content.php into
 *                                 Tentang Kami, Kebijakan Privasi and the articles.
 *                                 Safe to re-run — it skips anything an editor has
 *                                 already touched unless --force is passed.
 *
 * Ships inside the plugin so it runs on production too (Hostinger WP-CLI),
 * not just @wordpress/env. Idempotent-ish: skips items that already exist.
 * Inert unless invoked under WP-CLI.
 *
 * @package BlackrollCore
 */

if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

if ( ! class_exists( 'Blackroll_Seed_Command' ) ) {

	/**
	 * Seed command.
	 */
	class Blackroll_Seed_Command {

		private $media_cache = array();

		/**
		 * Seed all Blackroll content.
		 *
		 * ## EXAMPLES
		 *   wp blackroll seed
		 */
		public function __invoke() {
			$this->pages();
			$this->shades();
			$this->projects();
			$this->articles();
			WP_CLI::success( 'Blackroll content seeded (real SKU list + photos + copy).' );
		}

		/**
		 * Sideload a bundled seed asset into the Media Library.
		 *
		 * Public so it can be handed to the seed-content builders as a callable.
		 *
		 * @param string $rel Path relative to seed-assets/.
		 * @param string $alt Alt text.
		 * @return int Attachment ID, 0 on failure.
		 */
		public function media( $rel, $alt = '' ) {
			if ( isset( $this->media_cache[ $rel ] ) ) {
				return $this->media_cache[ $rel ];
			}
			$path = defined( 'BLACKROLL_CORE_DIR' ) ? BLACKROLL_CORE_DIR . 'seed-assets/' . $rel : '';
			if ( ! $path || ! file_exists( $path ) ) {
				// Loud, not silent: a missing asset means every image that depends
				// on it will render blank, and the seed would otherwise look clean.
				WP_CLI::warning( "seed asset missing on disk: seed-assets/{$rel}" );
				return 0;
			}
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			// Reuse an already-sideloaded copy instead of duplicating on re-runs.
			$existing = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'name'           => sanitize_title( pathinfo( $path, PATHINFO_FILENAME ) ),
				)
			);
			if ( ! empty( $existing ) ) {
				$this->media_cache[ $rel ] = (int) $existing[0];
				return $this->media_cache[ $rel ];
			}

			$uploads = wp_upload_dir();
			if ( ! empty( $uploads['error'] ) ) {
				WP_CLI::warning( 'uploads dir not usable: ' . $uploads['error'] );
				return 0;
			}

			$tmp = wp_tempnam( basename( $path ) );
			copy( $path, $tmp );
			$id = media_handle_sideload( array( 'name' => basename( $path ), 'tmp_name' => $tmp ), 0 );
			if ( is_wp_error( $id ) ) {
				@unlink( $tmp );
				// Without this the seed reports success while every image is blank.
				WP_CLI::warning( "sideload failed for {$rel}: " . $id->get_error_message() );
				return 0;
			}
			if ( $alt ) {
				update_post_meta( $id, '_wp_attachment_image_alt', $alt );
			}
			$this->media_cache[ $rel ] = $id;
			return $id;
		}

		private function post_exists_by_title( $title, $type ) {
			return (bool) $this->find_by_title( $title, $type );
		}

		private function find_by_title( $title, $type ) {
			$found = get_posts(
				array(
					'post_type'      => $type,
					'title'          => $title,
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			return empty( $found ) ? 0 : (int) $found[0];
		}

		private function ensure_page( $title, $slug, $template = '', $content = '', $parent = 0 ) {
			$path     = $parent ? ( get_post_field( 'post_name', $parent ) . '/' . $slug ) : $slug;
			$existing = get_page_by_path( $path );
			if ( $existing ) {
				return $existing->ID;
			}
			if ( '' === $content ) {
				$content = '<!-- wp:paragraph --><p>' . esc_html( $title ) . ' (konten contoh).</p><!-- /wp:paragraph -->';
			}
			$id = wp_insert_post(
				array(
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_parent'  => $parent,
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
			$media = array( $this, 'media' );

			$this->ensure_page( 'Tentang Kami', 'tentang-kami', '', blackroll_content_page_about( $media ) );
			$produk = $this->ensure_page( 'Produk', 'produk', '', '<!-- wp:pattern {"slug":"blackroll/home-product-preview"} /-->' );
			$this->ensure_page( 'Blinds Manual', 'blinds-manual', 'template-product', '<!-- wp:pattern {"slug":"blackroll/product-manual"} /-->', $produk );
			$this->ensure_page( 'Blinds Motorized', 'blinds-motorized', 'template-product', '<!-- wp:pattern {"slug":"blackroll/product-motorized"} /-->', $produk );
			$this->ensure_page( 'Material & Warna', 'material-warna', '', '<!-- wp:pattern {"slug":"blackroll/page-material-color"} /-->' );
			// Portfolio is the project CPT archive at /portofolio/ (archive-project.html) — no static page.
			$this->ensure_page( 'Kontak', 'kontak', '', '<!-- wp:pattern {"slug":"blackroll/page-contact"} /-->' );
			$this->ensure_page( 'Terima Kasih', 'terima-kasih', 'template-thankyou', '<!-- wp:pattern {"slug":"blackroll/contact-thankyou"} /-->' );
			$this->ensure_page( 'Pengiriman Gagal', 'gagal', 'template-thankyou', '<!-- wp:pattern {"slug":"blackroll/contact-failed"} /-->' );
			$this->ensure_page( 'Kebijakan Privasi', 'kebijakan-privasi', '', blackroll_content_page_privacy() );

			$blog = $this->ensure_page( 'Artikel', 'artikel' );

			$home = $this->ensure_page( 'Beranda', 'beranda' );
			if ( $home ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $home );
			}
			if ( $blog ) {
				update_option( 'page_for_posts', $blog );
			}
		}

		private function shades() {
			// Authoritative list from the client SKU sheet (2026-07-13).
			// [ display, sku, series, material_type, photo-slug|'' ]
			// Series: black = RBXL (accessory Hitam), white = RBPL (accessory Putih).
			$shades = array(
				array( 'ML003C', 'ML003C', 'white-series', 'blackout', 'ml003c' ),
				array( 'ML004D', 'ML004D', 'white-series', 'blackout', 'ml004d' ),
				array( 'ML005E', 'ML005E', 'white-series', 'blackout', 'ml005e' ),
				array( 'ML006F', 'ML006F', 'white-series', 'blackout', 'ml006f' ),
				array( 'ML007G', 'ML007G', 'white-series', 'blackout', 'ml007g' ),
				array( 'ML002B', 'ML002B', 'white-series', 'blackout', 'ml002b' ),
				array( 'ML001A', 'ML001A', 'black-series', 'blackout', 'ml001a' ),
				array( 'WD003C', 'WD003C', 'black-series', 'blackout', '' ),
				array( 'LD002B', 'LD002B', 'black-series', 'blackout', '' ),
				array( 'SV003C', 'SV003C', 'black-series', 'blackout', '' ),
				array( 'HG001A', 'HG001A', 'black-series', 'blackout', '' ),
				array( 'H003', 'H003', 'black-series', 'blackout', '' ),
				array( 'K004', 'K004', 'black-series', 'blackout', '' ),
				array( 'L003', 'L003', 'black-series', 'blackout', '' ),
				array( 'A004', 'A004', 'black-series', 'solar_screen', '' ),
				array( 'B001', 'B001', 'black-series', 'solar_screen', '' ),
				array( 'C003', 'C003', 'black-series', 'solar_screen', '' ),
				array( 'D003', 'D003', 'black-series', 'solar_screen', 'd001a' ),
				array( 'E003', 'E003', 'black-series', 'solar_screen', '' ),
				array( 'F001', 'F001', 'black-series', 'solar_screen', '' ),
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
			foreach ( blackroll_content_articles( array( $this, 'media' ) ) as $a ) {
				if ( get_page_by_path( $a['slug'], OBJECT, 'post' ) || $this->post_exists_by_title( $a['title'], 'post' ) ) {
					continue;
				}
				$id = wp_insert_post(
					array(
						'post_title'   => $a['title'],
						'post_name'    => $a['slug'],
						'post_status'  => 'publish',
						'post_type'    => 'post',
						'post_content' => $a['content'],
						'post_excerpt' => $a['excerpt'],
					)
				);
				if ( is_wp_error( $id ) ) {
					continue;
				}
				$cat = get_cat_ID( $a['category'] );
				if ( $cat ) {
					wp_set_post_categories( $id, array( $cat ) );
				}
				$att = $this->media( $a['image'], $a['alt'] );
				if ( $att ) {
					set_post_thumbnail( $id, $att );
				}
				WP_CLI::log( "article: {$a['title']}" );
			}
		}

		private function ensure_category() {
			foreach ( array( 'Panduan', 'Inspirasi', 'Produk' ) as $name ) {
				if ( ! get_cat_ID( $name ) ) {
					wp_insert_term( $name, 'category' );
				}
			}
		}

		/* -----------------------------------------------------------------
		 * `wp blackroll content` — editorial refresh
		 * -------------------------------------------------------------- */

		/**
		 * Write the real copy into pages/articles that still hold placeholder
		 * text (or everything, with --force). Creates anything missing.
		 *
		 * @param array $assoc Assoc args: force, dry-run.
		 */
		public function refresh_content( $assoc = array() ) {
			$force = ! empty( $assoc['force'] );
			$dry   = ! empty( $assoc['dry-run'] );
			$media = array( $this, 'media' );

			if ( $dry ) {
				WP_CLI::log( '(dry run — nothing will be written)' );
			}

			$this->write_page( 'Tentang Kami', 'tentang-kami', blackroll_content_page_about( $media ), $force, $dry );
			$this->write_page( 'Kebijakan Privasi', 'kebijakan-privasi', blackroll_content_page_privacy(), $force, $dry );

			$this->ensure_category();
			foreach ( blackroll_content_articles( $media ) as $a ) {
				$this->write_article( $a, $force, $dry );
			}

			WP_CLI::success( $dry ? 'Dry run complete.' : 'Blackroll copy applied.' );
		}

		/**
		 * True when the stored content is still one of the seed placeholders.
		 *
		 * @param string $content Stored post content.
		 * @return bool
		 */
		private function is_placeholder( $content ) {
			$content = trim( (string) $content );
			if ( '' === $content ) {
				return true;
			}
			foreach ( blackroll_content_placeholder_markers() as $marker ) {
				if ( false !== strpos( $content, $marker ) ) {
					return true;
				}
			}
			return false;
		}

		private function write_page( $title, $slug, $content, $force, $dry ) {
			$page = get_page_by_path( $slug );
			if ( ! $page ) {
				if ( $dry ) {
					WP_CLI::log( "would create page: {$slug}" );
					return;
				}
				$this->ensure_page( $title, $slug, '', $content );
				return;
			}
			if ( ! $force && ! $this->is_placeholder( $page->post_content ) ) {
				WP_CLI::log( "skip page (already edited): {$slug} — use --force to overwrite" );
				return;
			}
			if ( $dry ) {
				WP_CLI::log( "would update page: {$slug}" );
				return;
			}
			wp_update_post(
				array(
					'ID'           => $page->ID,
					'post_content' => $content,
					'post_status'  => 'publish',
				)
			);
			WP_CLI::log( "page updated: {$slug}" );
		}

		private function write_article( $a, $force, $dry ) {
			$post = get_page_by_path( $a['slug'], OBJECT, 'post' );
			if ( ! $post ) {
				// Catch posts created by an earlier seed under the auto-generated slug.
				$id   = $this->find_by_title( $a['title'], 'post' );
				$post = $id ? get_post( $id ) : null;
			}

			if ( ! $post ) {
				if ( $dry ) {
					WP_CLI::log( "would create article: {$a['slug']}" );
					return;
				}
				$id = wp_insert_post(
					array(
						'post_title'   => $a['title'],
						'post_name'    => $a['slug'],
						'post_status'  => 'publish',
						'post_type'    => 'post',
						'post_content' => $a['content'],
						'post_excerpt' => $a['excerpt'],
					)
				);
				if ( is_wp_error( $id ) ) {
					WP_CLI::warning( "failed: {$a['slug']}" );
					return;
				}
				$this->article_terms( $id, $a );
				WP_CLI::log( "article created: {$a['slug']}" );
				return;
			}

			if ( ! $force && ! $this->is_placeholder( $post->post_content ) ) {
				WP_CLI::log( "skip article (already edited): {$post->post_name} — use --force to overwrite" );
				return;
			}
			if ( $dry ) {
				WP_CLI::log( "would update article: {$a['slug']}" );
				return;
			}
			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_name'    => $a['slug'],
					'post_content' => $a['content'],
					'post_excerpt' => $a['excerpt'],
					'post_status'  => 'publish',
				)
			);
			$this->article_terms( $post->ID, $a );
			WP_CLI::log( "article updated: {$a['slug']}" );
		}

		private function article_terms( $id, $a ) {
			$cat = get_cat_ID( $a['category'] );
			if ( $cat ) {
				wp_set_post_categories( $id, array( $cat ) );
			}
			if ( ! has_post_thumbnail( $id ) ) {
				$att = $this->media( $a['image'], $a['alt'] );
				if ( $att ) {
					set_post_thumbnail( $id, $att );
				}
			}
		}
	}

	WP_CLI::add_command( 'blackroll seed', 'Blackroll_Seed_Command' );

	WP_CLI::add_command(
		'blackroll content',
		function ( $args, $assoc ) {
			$cmd = new Blackroll_Seed_Command();
			$cmd->refresh_content( $assoc );
		},
		array(
			'shortdesc' => 'Write the real Bahasa Indonesia copy into Tentang Kami, Kebijakan Privasi and the articles.',
			'synopsis'  => array(
				array(
					'name'        => 'force',
					'type'        => 'flag',
					'optional'    => true,
					'description' => 'Overwrite content even if an editor already changed it.',
				),
				array(
					'name'        => 'dry-run',
					'type'        => 'flag',
					'optional'    => true,
					'description' => 'Show what would change without writing.',
				),
			),
		)
	);
}
