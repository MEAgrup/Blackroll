<?php
/**
 * `wp blackroll doctor` — why are images not showing?
 *
 * Walks the whole image pipeline in the order it can break, and prints a
 * PASS/FAIL line per stage plus the exact fix. Written because a failed
 * sideload or a siteurl mismatch produces the same symptom (blank images)
 * from completely different causes, and guessing over SSH is slow.
 *
 * Inert unless invoked under WP-CLI.
 *
 * @package BlackrollCore
 */

if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

if ( ! class_exists( 'Blackroll_Doctor_Command' ) ) {

	/**
	 * Diagnostics for the image pipeline.
	 */
	class Blackroll_Doctor_Command {

		/** @var string[] Collected failures. */
		private $problems = array();

		/**
		 * Diagnose missing images end to end.
		 *
		 * ## EXAMPLES
		 *   wp blackroll doctor
		 */
		public function __invoke() {
			$this->section( '1. Theme & plugin' );
			$this->check_theme();

			$this->section( '2. Site URLs' );
			$this->check_urls();

			$this->section( '3. Theme-bundled images (hero rooms, OG, favicon)' );
			$this->check_theme_assets();

			$this->section( '4. Uploads directory' );
			$this->check_uploads();

			$this->section( '5. Image library (WebP support)' );
			$this->check_image_support();

			$this->section( '6. Media Library' );
			$this->check_media();

			$this->section( '7. Content coverage' );
			$this->check_coverage();

			$this->summary();
		}

		/* ---------------------------------------------------------- */

		private function section( $title ) {
			WP_CLI::log( '' );
			WP_CLI::log( WP_CLI::colorize( '%B' . $title . '%n' ) );
		}

		private function pass( $msg ) {
			WP_CLI::log( WP_CLI::colorize( '  %Gok%n   ' ) . $msg );
		}

		private function warn( $msg, $fix = '' ) {
			WP_CLI::log( WP_CLI::colorize( '  %Ywarn%n ' ) . $msg );
			if ( $fix ) {
				WP_CLI::log( '       → ' . $fix );
			}
		}

		private function fail( $msg, $fix = '' ) {
			WP_CLI::log( WP_CLI::colorize( '  %Rfail%n ' ) . $msg );
			if ( $fix ) {
				WP_CLI::log( '       → ' . $fix );
			}
			$this->problems[] = $msg . ( $fix ? ' — ' . $fix : '' );
		}

		private function info( $msg ) {
			WP_CLI::log( '       ' . $msg );
		}

		/* ---------------------------------------------------------- */

		private function check_theme() {
			$theme = wp_get_theme();
			$slug  = get_stylesheet();

			if ( 'blackroll' === $slug ) {
				$this->pass( "active theme: {$slug} ({$theme->get( 'Version' )})" );
			} else {
				$this->fail(
					"active theme is '{$slug}', not 'blackroll'",
					'wp theme activate blackroll'
				);
			}

			// is_plugin_active() lives in wp-admin and is not loaded under WP-CLI.
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active( 'blackroll-core/blackroll-core.php' ) ) {
				$this->pass( 'blackroll-core is active' );
			} else {
				$this->fail(
					'blackroll-core is NOT active — shades, portfolio and the selector render nothing',
					'wp plugin activate blackroll-core'
				);
			}

			$sizes   = get_intermediate_image_sizes();
			$wanted  = array( 'blackroll-card', 'blackroll-preview', 'blackroll-swatch' );
			$missing = array_diff( $wanted, $sizes );
			if ( empty( $missing ) ) {
				$this->pass( 'custom image sizes registered: ' . implode( ', ', $wanted ) );
			} else {
				$this->fail(
					'image sizes missing: ' . implode( ', ', $missing ) . ' (theme not loaded?)',
					'these come from themes/blackroll/inc/setup.php — confirm the theme is active'
				);
			}
		}

		private function check_urls() {
			$site = get_option( 'siteurl' );
			$home = get_option( 'home' );

			$this->info( "siteurl: {$site}" );
			$this->info( "home:    {$home}" );

			if ( wp_parse_url( $site, PHP_URL_HOST ) !== wp_parse_url( $home, PHP_URL_HOST ) ) {
				$this->fail(
					'siteurl and home point at different hosts — asset URLs will resolve to the wrong domain',
					'wp option update siteurl <url> && wp option update home <url>'
				);
			} else {
				$this->pass( 'siteurl and home agree' );
			}

			if ( 'https' === wp_parse_url( $home, PHP_URL_SCHEME ) ) {
				$this->pass( 'site runs on https' );
			} else {
				$this->warn(
					'site URL is http:// — browsers block http images on an https page (mixed content)',
					'switch siteurl/home to https and run: wp search-replace "http://<domain>" "https://<domain>" --skip-columns=guid --precise'
				);
			}
		}

		private function check_theme_assets() {
			$dir  = get_template_directory();
			$uri  = get_template_directory_uri();
			$want = array(
				'assets/images/rooms/ruang-tamu.webp',
				'assets/images/rooms/ruang-dapur.webp',
				'assets/images/rooms/kantor.webp',
				'assets/images/og-default.jpg',
				'assets/images/favicon.svg',
				'assets/images/map-placeholder.svg',
			);

			$missing = array();
			foreach ( $want as $rel ) {
				if ( ! file_exists( trailingslashit( $dir ) . $rel ) ) {
					$missing[] = $rel;
				}
			}

			if ( empty( $missing ) ) {
				$this->pass( count( $want ) . ' theme image files present on disk' );
				$this->info( 'test one in a browser: ' . trailingslashit( $uri ) . 'assets/images/rooms/ruang-tamu.webp' );
			} else {
				$this->fail(
					'theme images missing on disk: ' . implode( ', ', $missing ),
					're-sync the theme: rsync -a --delete ~/src/blackroll/themes/blackroll/ ' . trailingslashit( $dir )
				);
			}
		}

		private function check_uploads() {
			$up = wp_get_upload_dir();

			if ( ! empty( $up['error'] ) ) {
				$this->fail( 'uploads dir error: ' . $up['error'], 'fix ownership/permissions on wp-content/uploads' );
				return;
			}

			$this->info( 'path: ' . $up['basedir'] );
			$this->info( 'url:  ' . $up['baseurl'] );

			if ( is_dir( $up['basedir'] ) && is_writable( $up['basedir'] ) ) {
				$this->pass( 'uploads dir exists and is writable' );
			} else {
				$this->fail(
					'uploads dir is not writable — every sideload during seed fails silently',
					'chmod 755 ' . $up['basedir'] . ' (and check the directory owner)'
				);
			}

			$htaccess = trailingslashit( $up['basedir'] ) . '.htaccess';
			if ( file_exists( $htaccess ) ) {
				$body = (string) file_get_contents( $htaccess );
				// deploy/uploads.htaccess blocks PHP only; a broad deny blocks images too.
				if ( preg_match( '/^\s*(Deny from all|Require all denied)/mi', $body ) ) {
					$this->fail(
						'uploads/.htaccess denies all requests — images return 403',
						'it should only block PHP execution; restore deploy/uploads.htaccess'
					);
				} else {
					$this->pass( 'uploads/.htaccess present and not blocking images' );
				}
			}
		}

		private function check_image_support() {
			$has_gd      = extension_loaded( 'gd' );
			$has_imagick = extension_loaded( 'imagick' );

			if ( ! $has_gd && ! $has_imagick ) {
				$this->fail(
					'neither GD nor Imagick is loaded — WordPress cannot generate any image sizes',
					'enable the GD extension for this PHP version in hPanel → PHP Configuration'
				);
				return;
			}
			$this->pass( 'image extensions: ' . trim( ( $has_gd ? 'GD ' : '' ) . ( $has_imagick ? 'Imagick' : '' ) ) );

			if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
				$this->pass( 'WebP is supported — the seed photos can be processed' );
			} else {
				$this->fail(
					'WebP is NOT supported by this PHP build — every .webp sideload fails, so all product/portfolio/article photos stay empty',
					'enable WebP support in GD (hPanel → PHP Configuration), then re-run: wp blackroll seed'
				);
			}

			$allowed = get_allowed_mime_types();
			if ( in_array( 'image/webp', $allowed, true ) ) {
				$this->pass( 'image/webp is an allowed upload type' );
			} else {
				$this->fail(
					'image/webp is not in the allowed mime types — uploads are rejected before they start',
					'a security plugin is likely filtering upload_mimes; allow image/webp'
				);
			}
		}

		private function check_media() {
			$total = (int) wp_count_posts( 'attachment' )->inherit;
			if ( $total < 1 ) {
				$this->fail(
					'the Media Library is empty — nothing has been seeded yet',
					'wp blackroll seed'
				);
				return;
			}
			$this->pass( "{$total} attachments in the Media Library" );

			$sample = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'post_mime_type' => 'image',
					'posts_per_page' => 20,
					'fields'         => 'ids',
				)
			);

			$missing_files = 0;
			$no_subsizes   = 0;
			foreach ( $sample as $id ) {
				$file = get_attached_file( $id );
				if ( ! $file || ! file_exists( $file ) ) {
					$missing_files++;
					continue;
				}
				$meta = wp_get_attachment_metadata( $id );
				if ( empty( $meta['sizes'] ) ) {
					$no_subsizes++;
				}
			}

			if ( $missing_files > 0 ) {
				$this->fail(
					"{$missing_files} of " . count( $sample ) . ' sampled attachments have no file on disk — the database knows about images the server does not have',
					'this happens when a DB is migrated without wp-content/uploads. Re-run: wp blackroll seed'
				);
			} else {
				$this->pass( 'sampled attachment files all exist on disk' );
			}

			if ( $no_subsizes > 0 ) {
				$this->warn(
					"{$no_subsizes} sampled attachments have no generated sub-sizes — cards and swatches fall back to full-size files",
					'wp media regenerate --yes'
				);
			} else {
				$this->pass( 'sub-sizes generated for sampled attachments' );
			}
		}

		private function check_coverage() {
			$this->coverage_for( 'shade', 'Shades', true );
			$this->coverage_for( 'project', 'Portfolio projects', false );
			$this->coverage_for( 'post', 'Articles', false );
		}

		/**
		 * Report how many posts of a type actually carry an image.
		 *
		 * @param string $type       Post type.
		 * @param string $label      Human label.
		 * @param bool   $check_meta Also accept the preview_image meta (shades).
		 */
		private function coverage_for( $type, $label, $check_meta ) {
			$ids = get_posts(
				array(
					'post_type'      => $type,
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);
			$total = count( $ids );
			if ( ! $total ) {
				$this->warn( "{$label}: none exist yet", 'wp blackroll seed' );
				return;
			}

			$with = 0;
			foreach ( $ids as $id ) {
				if ( get_post_thumbnail_id( $id ) ) {
					$with++;
					continue;
				}
				if ( $check_meta && (int) get_post_meta( $id, 'preview_image', true ) ) {
					$with++;
				}
			}

			if ( 0 === $with ) {
				$this->fail(
					"{$label}: 0 of {$total} have an image — this is why that section renders blank",
					'wp blackroll seed  (watch for "sideload failed" warnings)'
				);
			} elseif ( $with < $total ) {
				// Expected: not every SKU in the client sheet has a photo yet.
				$this->pass( "{$label}: {$with} of {$total} have an image" );
			} else {
				$this->pass( "{$label}: all {$total} have an image" );
			}
		}

		private function summary() {
			WP_CLI::log( '' );
			if ( empty( $this->problems ) ) {
				WP_CLI::success( 'No blocking problems found in the image pipeline.' );
				WP_CLI::log( 'If images still look missing in a browser, check the page source for the image URL and open it directly — a 404 points at the theme sync, a 403 at .htaccess or hotlink protection, and a stale HTML page at the LiteSpeed cache (wp litespeed-purge all).' );
				return;
			}
			WP_CLI::log( WP_CLI::colorize( '%R' . count( $this->problems ) . ' problem(s) found:%n' ) );
			foreach ( $this->problems as $i => $p ) {
				WP_CLI::log( '  ' . ( $i + 1 ) . '. ' . $p );
			}
			WP_CLI::halt( 1 );
		}
	}

	WP_CLI::add_command(
		'blackroll doctor',
		'Blackroll_Doctor_Command',
		array( 'shortdesc' => 'Diagnose why images are not rendering (theme assets, uploads, WebP support, media coverage).' )
	);
}
