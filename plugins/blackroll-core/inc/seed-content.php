<?php
/**
 * Real editorial content (Bahasa Indonesia) for the seed/refresh commands.
 *
 * Everything here is written from Blackroll's own brand material shipped in
 * `seed-assets/` (5-lapis blackout spec sheet, panduan pemasangan, spesifikasi
 * produk) plus the locked config (WA number, materials, page routes). Nothing
 * is invented: no founding year, no warranty terms, no pricing — those need
 * client confirmation before they go on the site.
 *
 * Content lives in PHP (not the DB) so it is versioned, reviewable in PRs and
 * re-appliable with `wp blackroll content`.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Marker strings written by the old placeholder seed. `wp blackroll content`
 * only overwrites posts still carrying one of these (unless --force).
 *
 * @return string[]
 */
function blackroll_content_placeholder_markers() {
	return array(
		'(konten contoh)',
		'Draft — konten ditulis tim konten.',
	);
}

/* -------------------------------------------------------------------------
 * Block markup helpers
 * ---------------------------------------------------------------------- */

/**
 * Paragraph block.
 *
 * @param string $html  Inline HTML (already trusted, authored here).
 * @param string $attrs Optional JSON attributes, e.g. '{"fontSize":"large"}'.
 * @return string
 */
function blackroll_content_p( $html, $attrs = '' ) {
	$class = '';
	if ( false !== strpos( $attrs, '"fontSize":"large"' ) ) {
		$class = ' class="has-large-font-size"';
	} elseif ( false !== strpos( $attrs, '"fontSize":"small"' ) ) {
		$class = ' class="has-small-font-size"';
	}
	$open = $attrs ? '<!-- wp:paragraph ' . $attrs . ' -->' : '<!-- wp:paragraph -->';
	return $open . '<p' . $class . '>' . $html . '</p><!-- /wp:paragraph -->' . "\n\n";
}

/**
 * Heading block.
 *
 * @param int    $level Heading level (2–4).
 * @param string $text  Heading text.
 * @return string
 */
function blackroll_content_h( $level, $text ) {
	$size  = ( 2 === $level ) ? 'x-large' : 'large';
	$attrs = '{"level":' . (int) $level . ',"fontSize":"' . $size . '"}';
	return '<!-- wp:heading ' . $attrs . ' --><h' . (int) $level .
		' class="wp-block-heading has-' . $size . '-font-size">' . $text .
		'</h' . (int) $level . '><!-- /wp:heading -->' . "\n\n";
}

/**
 * List block.
 *
 * @param string[] $items   Item HTML.
 * @param bool     $ordered Ordered list.
 * @return string
 */
function blackroll_content_list( array $items, $ordered = false ) {
	$tag  = $ordered ? 'ol' : 'ul';
	$out  = $ordered ? '<!-- wp:list {"ordered":true} -->' : '<!-- wp:list -->';
	$out .= '<' . $tag . ' class="wp-block-list">';
	foreach ( $items as $item ) {
		$out .= '<!-- wp:list-item --><li>' . $item . '</li><!-- /wp:list-item -->';
	}
	$out .= '</' . $tag . '><!-- /wp:list -->' . "\n\n";
	return $out;
}

/**
 * Image block for a sideloaded seed asset. Returns '' when the asset is
 * missing so content never renders a broken image.
 *
 * @param callable|null $media   Resolver: ( string $rel, string $alt ) => int attachment ID.
 * @param string        $rel     Path relative to seed-assets/.
 * @param string        $alt     Alt text (Bahasa Indonesia).
 * @param string        $caption Optional caption.
 * @return string
 */
function blackroll_content_image( $media, $rel, $alt, $caption = '' ) {
	if ( ! is_callable( $media ) ) {
		return '';
	}
	$id = (int) call_user_func( $media, $rel, $alt );
	if ( ! $id ) {
		return '';
	}
	$url = wp_get_attachment_image_url( $id, 'large' );
	if ( ! $url ) {
		return '';
	}
	$fig = '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) .
		'" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $id . '"/>';
	if ( $caption ) {
		$fig .= '<figcaption class="wp-element-caption">' . $caption . '</figcaption>';
	}
	$fig .= '</figure>';
	return '<!-- wp:image {"id":' . $id . ',"sizeSlug":"large","linkDestination":"none"} -->' .
		$fig . '<!-- /wp:image -->' . "\n\n";
}

/**
 * Closing CTA — reuses the theme pattern so the band stays in one place.
 *
 * @return string
 */
function blackroll_content_cta() {
	return '<!-- wp:pattern {"slug":"blackroll/cta-contact"} /-->' . "\n";
}

/* -------------------------------------------------------------------------
 * Pages
 * ---------------------------------------------------------------------- */

/**
 * "Tentang Kami" page body.
 *
 * @param callable|null $media Media resolver.
 * @return string
 */
function blackroll_content_page_about( $media = null ) {
	$out = '';

	$out .= blackroll_content_p(
		'<strong>Blackroll</strong> membuat roller blinds untuk rumah, apartemen dan kantor di Indonesia — dengan satu prinsip sederhana: <em>affordable premium</em>. Material dan mekanisme kelas premium, tanpa harga yang membuat Anda menunda memasang tirai di seluruh ruangan.',
		'{"fontSize":"large"}'
	);

	$out .= blackroll_content_p(
		'Kami fokus pada satu kategori produk dan mengerjakannya sampai detail: kain yang diproduksi dengan teknologi terkini, headrail dan rantai yang halus dipakai bertahun-tahun, serta pemasangan yang rapi dan presisi. Sudah lebih dari <strong>100 proyek</strong> selesai — residensial, kantor dan apartemen — dan sebagian besar datang dari kontraktor serta desainer interior yang memesan berulang.'
	);

	$out .= blackroll_content_h( 2, 'Standar produk kami' );
	$out .= blackroll_content_image(
		$media,
		'tech/inspirasi-ruang.webp',
		'Spesifikasi produk roller blinds Blackroll: teknologi terkini, tahan air dan minyak, desain minimalis, banyak pilihan warna'
	);
	$out .= blackroll_content_list(
		array(
			'<strong>Diproduksi dengan teknologi terkini.</strong> Kain melalui proses pelapisan modern, bukan kain tirai biasa yang dipotong ulang.',
			'<strong>Tahan air &amp; tahan minyak.</strong> Cipratan air dan uap minyak tidak langsung meresap — cukup dilap. Aman untuk dapur dan area basah.',
			'<strong>Anti bakteri dan tidak mudah berjamur.</strong> Penting untuk iklim lembap Indonesia dan ruangan yang jarang kena matahari.',
			'<strong>Desain minimalis.</strong> Headrail ramping, tanpa ornamen, menyatu dengan interior modern dan tidak "memakan" bidang jendela.',
			'<strong>Banyak pilihan warna.</strong> Dua seri warna aksesori — Black Series dan White Series — dengan pilihan kain netral sampai gelap.',
		)
	);

	$out .= blackroll_content_h( 2, 'Yang kami kerjakan' );
	$out .= blackroll_content_p(
		'Dua tipe pengoperasian, empat jenis material. Kombinasinya dipilih berdasarkan fungsi ruangan, bukan sekadar selera warna.'
	);
	$out .= blackroll_content_list(
		array(
			'<a href="/produk/blinds-manual/"><strong>Blinds Manual</strong></a> — dioperasikan dengan rantai. Pilihan paling umum untuk kamar, ruang tamu, dan jendela yang mudah dijangkau.',
			'<a href="/produk/blinds-motorized/"><strong>Blinds Motorized</strong></a> — dioperasikan dengan remote. Untuk jendela tinggi, bidang kaca lebar, atau ruangan dengan banyak jendela sekaligus.',
			'<strong>Blackout</strong> — 5 lapis, menahan cahaya, panas, dan menjaga privasi. Untuk kamar tidur dan ruang media.',
			'<strong>Solar Screen</strong> — menyaring silau dan panas tetapi ruangan tetap terang dan pandangan ke luar terjaga. Untuk kantor dan ruang kerja.',
			'<strong>Dimout</strong> — meredupkan tanpa menggelapkan total.',
			'<strong>Zebra Blinds</strong> — pita transparan dan solid berselang-seling, tingkat cahaya diatur dengan menggeser posisi kain.',
		)
	);
	$out .= blackroll_content_p(
		'Belum yakin material mana yang cocok? Lihat <a href="/material-warna/">Material &amp; Warna</a> untuk membandingkan pilihan kain dan kode warnanya.'
	);

	$out .= blackroll_content_h( 2, 'Cara kami bekerja' );
	$out .= blackroll_content_list(
		array(
			'<strong>Konsultasi.</strong> Ceritakan ruangannya — arah jendela, fungsi ruang, dan hasil yang Anda inginkan. Kami bantu tentukan material dan warnanya.',
			'<strong>Ukur.</strong> Pengukuran dilakukan per jendela agar kain menutup sempurna dan tidak ada celah cahaya yang tidak perlu.',
			'<strong>Produksi.</strong> Setiap unit dibuat sesuai ukuran jendela Anda, lengkap dengan bracket dan aksesori sesuai seri warna yang dipilih.',
			'<strong>Pemasangan.</strong> Dipasang rapi oleh tim kami, atau kirim siap pasang sendiri — paket sudah termasuk sekrup, fischer dan wall clip, dengan panduan langkah demi langkah.',
		),
		true
	);

	$out .= blackroll_content_h( 2, 'Dipercaya kontraktor' );
	$out .= blackroll_content_p(
		'Pekerjaan proyek menuntut dua hal: konsistensi kualitas antar unit dan jadwal yang bisa dipegang. Itu yang kami jaga. Lihat hasilnya di <a href="/portofolio/">portofolio kami</a>.'
	);

	$out .= blackroll_content_cta();

	return trim( $out );
}

/**
 * "Kebijakan Privasi" page body.
 *
 * Drafted as a working baseline covering how this site actually handles data
 * (Sebari lead form, hosting logs, Search Console, no GA4 per D2).
 * ⚠️ Needs a legal/client review pass before go-live.
 *
 * @return string
 */
function blackroll_content_page_privacy() {
	$wa   = blackroll_wa_display();
	$link = blackroll_wa_link();
	$out  = '';

	$out .= blackroll_content_p(
		'Terakhir diperbarui: ' . date_i18n( 'j F Y' ) . '.',
		'{"fontSize":"small"}'
	);
	$out .= blackroll_content_p(
		'Halaman ini menjelaskan data apa yang kami kumpulkan ketika Anda menggunakan situs Blackroll, untuk apa data itu dipakai, dan hak apa yang Anda miliki atas data tersebut.'
	);

	$out .= blackroll_content_h( 2, 'Data yang kami kumpulkan' );
	$out .= blackroll_content_list(
		array(
			'<strong>Data yang Anda kirim sendiri.</strong> Melalui formulir konsultasi di halaman <a href="/kontak/">Kontak</a>: nama, nomor WhatsApp, dan alamat email. Ketiganya wajib agar kami dapat menghubungi Anda kembali.',
			'<strong>Isi percakapan.</strong> Jika Anda menghubungi kami lewat WhatsApp, isi percakapan tersimpan di perangkat dan akun WhatsApp kami.',
			'<strong>Data teknis.</strong> Server web mencatat alamat IP, jenis perangkat dan peramban, serta halaman yang dibuka. Catatan ini bersifat teknis dan digunakan untuk keamanan serta pemeliharaan situs.',
		)
	);
	$out .= blackroll_content_p(
		'Kami <strong>tidak</strong> meminta data pembayaran, nomor identitas, atau data pribadi yang bersifat spesifik melalui situs ini.'
	);

	$out .= blackroll_content_h( 2, 'Tujuan penggunaan data' );
	$out .= blackroll_content_list(
		array(
			'Menghubungi Anda kembali untuk konsultasi, penawaran, pengukuran, dan penjadwalan pemasangan.',
			'Menindaklanjuti pertanyaan tentang produk, pesanan, atau layanan purna jual.',
			'Menjaga keamanan situs dan mencegah penyalahgunaan formulir (spam).',
			'Memahami halaman mana yang paling dicari pengunjung, sehingga informasi produk dapat kami perbaiki.',
		)
	);

	$out .= blackroll_content_h( 2, 'Dasar pemrosesan' );
	$out .= blackroll_content_p(
		'Kami memproses data pribadi Anda berdasarkan <strong>persetujuan</strong> yang Anda berikan saat mengirimkan formulir, serta untuk memenuhi permintaan Anda sebagai calon pelanggan. Pemrosesan mengacu pada Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi.'
	);

	$out .= blackroll_content_h( 2, 'Layanan pihak ketiga' );
	$out .= blackroll_content_p(
		'Agar situs dan alur konsultasi berjalan, sebagian data diproses oleh penyedia layanan berikut:'
	);
	$out .= blackroll_content_list(
		array(
			'<strong>Sebari</strong> — memproses pengiriman formulir konsultasi dan meneruskan datanya ke tim kami melalui WhatsApp.',
			'<strong>Penyedia hosting</strong> — menyimpan situs beserta log servernya.',
			'<strong>Google Search Console</strong> — menampilkan data pencarian dalam bentuk agregat dan anonim (bukan data per individu).',
			'<strong>Google Maps</strong> — menampilkan lokasi kami di halaman Kontak apabila peta dimuat.',
		)
	);
	$out .= blackroll_content_p(
		'Kami tidak menjual, menyewakan, atau memperdagangkan data pribadi Anda kepada pihak lain.'
	);

	$out .= blackroll_content_h( 2, 'Cookie' );
	$out .= blackroll_content_p(
		'Situs ini menggunakan cookie teknis seperlunya — misalnya untuk sesi login pengelola situs, preferensi bahasa, dan cache halaman agar situs terbuka lebih cepat. Situs ini <strong>tidak memasang Google Analytics maupun cookie iklan pihak ketiga</strong>. Anda dapat menghapus atau memblokir cookie melalui pengaturan peramban tanpa kehilangan akses ke isi situs.'
	);

	$out .= blackroll_content_h( 2, 'Penyimpanan data' );
	$out .= blackroll_content_p(
		'Data kontak disimpan selama masih relevan untuk hubungan bisnis kita — termasuk masa layanan purna jual — atau sampai Anda meminta penghapusan. Log teknis server disimpan dalam jangka pendek sesuai kebijakan penyedia hosting.'
	);

	$out .= blackroll_content_h( 2, 'Keamanan' );
	$out .= blackroll_content_p(
		'Seluruh halaman disajikan melalui koneksi terenkripsi (HTTPS). Akses ke pengelolaan situs dibatasi hanya untuk pengguna yang berwenang. Tidak ada sistem yang aman sepenuhnya, namun kami menerapkan pengamanan yang wajar dan meninjaunya secara berkala.'
	);

	$out .= blackroll_content_h( 2, 'Hak Anda' );
	$out .= blackroll_content_list(
		array(
			'Meminta informasi mengenai data pribadi Anda yang kami simpan.',
			'Meminta koreksi apabila data Anda tidak akurat.',
			'Meminta penghapusan data Anda dari catatan kami.',
			'Menarik persetujuan dan berhenti menerima komunikasi dari kami, kapan saja.',
		)
	);
	$out .= blackroll_content_p(
		'Untuk menggunakan hak-hak tersebut, hubungi kami melalui WhatsApp di <a href="' . esc_url( $link ) . '" rel="noopener">' . esc_html( $wa ) . '</a>. Permintaan kami tindak lanjuti dalam waktu wajar setelah identitas pemohon dapat kami pastikan.'
	);

	$out .= blackroll_content_h( 2, 'Anak-anak' );
	$out .= blackroll_content_p(
		'Situs ini ditujukan untuk pengguna dewasa. Kami tidak dengan sengaja mengumpulkan data pribadi anak-anak. Jika hal itu terjadi, beri tahu kami agar datanya dapat kami hapus.'
	);

	$out .= blackroll_content_h( 2, 'Perubahan kebijakan' );
	$out .= blackroll_content_p(
		'Kebijakan ini dapat kami perbarui bila layanan atau ketentuan hukum berubah. Versi terbaru selalu tersedia di halaman ini, dengan tanggal pembaruan tercantum di bagian atas.'
	);

	$out .= blackroll_content_h( 2, 'Hubungi kami' );
	$out .= blackroll_content_p(
		'Pertanyaan mengenai kebijakan ini dapat disampaikan melalui WhatsApp <a href="' . esc_url( $link ) . '" rel="noopener">' . esc_html( $wa ) . '</a> atau melalui halaman <a href="/kontak/">Kontak</a>.'
	);

	return trim( $out );
}

/* -------------------------------------------------------------------------
 * Articles
 * ---------------------------------------------------------------------- */

/**
 * The article set: real bodies, excerpts (used as meta-description fallback),
 * category and featured image.
 *
 * @param callable|null $media Media resolver.
 * @return array<int,array<string,string>>
 */
function blackroll_content_articles( $media = null ) {
	return array(
		array(
			'title'    => 'Panduan Pemasangan Roller Blinds: Dinding vs Depan Kusen',
			'slug'     => 'panduan-pemasangan-roller-blinds',
			'category' => 'Panduan',
			'image'    => 'tech/panduan-pasang-1.webp',
			'alt'      => 'Panduan pemasangan roller blinds Blackroll: metode pada dinding dan depan kusen',
			'excerpt'  => 'Dua metode pemasangan roller blinds, alat yang perlu disiapkan, dan langkah A–D untuk masing-masing metode. Bisa dikerjakan sendiri dalam waktu kurang dari satu jam per jendela.',
			'content'  => blackroll_content_article_install( $media ),
		),
		array(
			'title'    => 'Kenapa Blackout Blackroll Menahan Cahaya 100%: 5 Lapisan Teknologi',
			'slug'     => 'blackout-5-lapisan-teknologi',
			'category' => 'Produk',
			'image'    => 'tech/blackout-5-lapis.webp',
			'alt'      => 'Struktur 5 lapis kain blackout Blackroll untuk pemadaman cahaya maksimal',
			'excerpt'  => 'Kain blackout Blackroll disusun dari lima lapisan berbeda. Ini fungsi tiap lapisan, dan kenapa banyak tirai berlabel blackout tetap bocor cahaya.',
			'content'  => blackroll_content_article_blackout( $media ),
		),
		array(
			'title'    => 'Tahan Air, Tahan Minyak, Anti Jamur: Roller Blinds untuk Dapur',
			'slug'     => 'roller-blinds-untuk-dapur',
			'category' => 'Produk',
			'image'    => 'tech/tahan-air-minyak.webp',
			'alt'      => 'Kain roller blinds Blackroll tahan air dan tahan minyak, anti bakteri dan tidak berjamur',
			'excerpt'  => 'Uap minyak, lembap, dan jamur membuat tirai dapur cepat rusak. Kenapa kain berlapis pelindung lebih tahan, dan cara merawatnya agar bertahun-tahun tetap bersih.',
			'content'  => blackroll_content_article_kitchen( $media ),
		),
		array(
			'title'    => 'Memilih Roller Blinds per Ruangan: Ruang Tamu, Kantor, Dapur, dan Kamar',
			'slug'     => 'memilih-roller-blinds-per-ruangan',
			'category' => 'Inspirasi',
			'image'    => 'lifestyle/ruang-tamu.webp',
			'alt'      => 'Roller blinds Blackroll terpasang di ruang tamu bergaya modern',
			'excerpt'  => 'Material yang tepat berbeda untuk tiap ruangan. Panduan singkat memilih blackout, solar screen, dimout, atau zebra berdasarkan fungsi ruang dan arah jendela.',
			'content'  => blackroll_content_article_rooms( $media ),
		),
	);
}

/**
 * Article: installation guide.
 *
 * @param callable|null $media Media resolver.
 * @return string
 */
function blackroll_content_article_install( $media = null ) {
	$out = '';

	$out .= blackroll_content_p(
		'Roller blinds Blackroll dikirim siap pasang — bracket sudah terpasang pada headrail, sekrup dan fischer sudah ada di dalam paket. Yang perlu Anda putuskan hanya satu hal di awal: <strong>dipasang pada dinding, atau di depan kusen?</strong> Pilihan itu menentukan titik pengeboran dan hasil akhirnya.',
		'{"fontSize":"large"}'
	);

	$out .= blackroll_content_h( 2, 'Dinding atau depan kusen?' );
	$out .= blackroll_content_p(
		'Keduanya sama-sama memasang blinds di sisi luar bidang kaca, bedanya pada media yang dibor.'
	);
	$out .= blackroll_content_list(
		array(
			'<strong>Pemasangan pada dinding</strong> — headrail dipasang pada tembok di atas kusen. Kain jatuh melewati bidang kusen, sehingga celah cahaya di sisi kanan-kiri lebih kecil. Pilihan terbaik untuk kamar tidur dan material blackout. Perlu fischer karena media pemasangannya tembok.',
			'<strong>Pemasangan depan kusen</strong> — headrail dipasang langsung pada kusen (kayu atau aluminium). Lebih ringkas, cocok bila di atas jendela tidak ada bidang tembok yang cukup, atau bila Anda tidak ingin mengebor tembok. Tidak memerlukan fischer.',
		)
	);
	$out .= blackroll_content_p(
		'Ragu? Untuk kamar tidur pilih pemasangan pada dinding. Untuk kantor, dapur, dan ruangan yang tetap ingin terang, pemasangan depan kusen sudah memadai.'
	);

	$out .= blackroll_content_h( 2, 'Siapkan dulu sebelum mulai' );
	$out .= blackroll_content_image(
		$media,
		'tech/panduan-pasang-2.webp',
		'Persiapan alat pemasangan roller blinds: meteran, pensil, bor portabel, tangga, sekrup, fischer dan wall clip'
	);
	$out .= blackroll_content_p( '<strong>Alat yang Anda siapkan sendiri:</strong>' );
	$out .= blackroll_content_list(
		array( 'Meteran', 'Pensil', 'Bor portabel', 'Tangga' )
	);
	$out .= blackroll_content_p( '<strong>Yang sudah ada di dalam paket Blackroll:</strong>' );
	$out .= blackroll_content_list(
		array(
			'Set roller blinds (bracket sudah terpasang)',
			'2 pcs ceiling/wall clip',
			'2 pcs sekrup',
			'2 pcs fischer — dipakai untuk pemasangan pada tembok',
		)
	);
	$out .= blackroll_content_p(
		'Buka kemasan dengan hati-hati dan periksa kelengkapannya terlebih dahulu. Baca panduan sampai selesai sebelum mulai mengebor — dua menit membaca menghemat satu lubang yang salah.'
	);

	$out .= blackroll_content_h( 3, 'Perhatian: keamanan anak' );
	$out .= blackroll_content_p(
		'<strong>Rantai roller blinds dapat membahayakan anak-anak.</strong> Selalu jauhkan rantai dari jangkauan anak. Pindahkan sofa, meja, atau benda lain yang memungkinkan anak memanjat dan meraih rantai. Untuk kamar anak, pertimbangkan <a href="/produk/blinds-motorized/">versi motorized</a> yang tidak menggunakan rantai sama sekali.'
	);

	$out .= blackroll_content_h( 2, 'Metode 1 — Pemasangan pada dinding' );
	$out .= blackroll_content_list(
		array(
			'<strong>Ukur dan tandai.</strong> Ukur lebar kusen, lalu kurangi sisi kanan dan kiri masing-masing <strong>5 cm</strong>; tandai dengan pensil. Untuk posisi ketinggiannya, beri jarak <strong>5 cm</strong> dari kusen ke atas, lalu tandai.',
			'<strong>Bor dan pasang fischer.</strong> Buat lubang pada dinding tepat di titik yang sudah ditandai, lalu masukkan fischer. Ulangi untuk lubang kedua.',
			'<strong>Pasang wall clip.</strong> Sekrup dan kencangkan wall clip pada kedua lubang tadi — inilah dudukan headrail.',
			'<strong>Kunci headrail.</strong> Kaitkan bagian belakang headrail pada wall clip, lalu dorong sampai terdengar bunyi <em>snap</em> — tanda headrail sudah terkunci sempurna.',
		),
		true
	);

	$out .= blackroll_content_h( 2, 'Metode 2 — Pemasangan depan kusen' );
	$out .= blackroll_content_list(
		array(
			'<strong>Ukur dan tandai.</strong> Sama seperti metode pertama: ukur lebar kusen, kurangi sisi kanan dan kiri masing-masing <strong>5 cm</strong>, tandai dengan pensil.',
			'<strong>Bor kusen.</strong> Buat lubang pada kusen di titik yang sudah ditandai. Ulangi pada lubang kedua. Fischer tidak diperlukan di sini.',
			'<strong>Pasang wall clip.</strong> Sekrup dan kencangkan wall clip pada lubang yang sudah dibuat.',
			'<strong>Kunci headrail.</strong> Kaitkan bagian belakang headrail pada wall clip hingga terkunci sempurna.',
		),
		true
	);

	$out .= blackroll_content_h( 2, 'Cek akhir' );
	$out .= blackroll_content_list(
		array(
			'Tarik rantai perlahan — kain harus turun dan naik lurus, tidak menyerong ke satu sisi.',
			'Gulung kain penuh ke atas, lalu perhatikan headrail: jika miring, kemungkinan kedua lubang tidak sejajar. Lepas satu sisi dan sesuaikan.',
			'Pastikan kedua ujung headrail benar-benar terkunci pada clip — headrail yang belum <em>snap</em> akan terlepas saat kain ditarik.',
			'Rapikan posisi rantai ke sisi yang jauh dari jangkauan anak.',
		)
	);

	$out .= blackroll_content_h( 2, 'Perawatan' );
	$out .= blackroll_content_p(
		'Cukup lap permukaan kain dengan kain lembap. Untuk noda membandel, gunakan sedikit sabun cair ringan dan lap searah. Jangan direndam, dicuci dengan mesin, atau disikat keras — lapisan pelindung kain bekerja optimal jika permukaannya tidak digosok kasar.'
	);

	$out .= blackroll_content_p(
		'Tidak ingin memasang sendiri? Tim kami dapat melakukan pengukuran dan pemasangan. <a href="/kontak/">Hubungi kami</a> dengan menyebutkan jumlah dan ukuran kasar jendela Anda.'
	);

	$out .= blackroll_content_cta();

	return trim( $out );
}

/**
 * Article: blackout technology.
 *
 * @param callable|null $media Media resolver.
 * @return string
 */
function blackroll_content_article_blackout( $media = null ) {
	$out = '';

	$out .= blackroll_content_p(
		'Banyak tirai dijual dengan label "blackout", tetapi ketika dipasang ruangan tetap remang — bukan gelap. Penyebabnya hampir selalu sama: kainnya hanya kain tebal berwarna gelap, bukan kain yang memang disusun berlapis untuk menahan cahaya.',
		'{"fontSize":"large"}'
	);
	$out .= blackroll_content_p(
		'Kain blackout Blackroll dibuat dari <strong>lima lapisan berbeda</strong>, masing-masing dengan tugas sendiri. Kombinasi itulah yang membuatnya menahan cahaya, menahan panas, dan menjaga privasi ruangan sekaligus.'
	);

	$out .= blackroll_content_image(
		$media,
		'tech/blackout-5-lapis.webp',
		'Lima lapisan kain blackout Blackroll untuk pemadaman cahaya maksimal'
	);

	$out .= blackroll_content_h( 2, 'Lima lapisan itu' );
	$out .= blackroll_content_list(
		array(
			'<strong>Lapisan perlindungan tahan air dan minyak.</strong> Lapisan terluar yang menghadap ruangan. Membuat cipratan air dan uap minyak tidak langsung meresap ke serat kain — cukup dilap, tidak meninggalkan bekas.',
			'<strong>Color lock fabric.</strong> Mengunci pigmen warna di tempatnya. Ini yang menjaga warna tidak luntur atau menguning setelah bertahun-tahun terkena sinar matahari langsung.',
			'<strong>Black out powder layer.</strong> Inti dari kain ini: lapisan bubuk padat yang menghalangi jalur cahaya. Cahaya yang menembus lapisan luar berhenti di sini, bukan diteruskan ke dalam ruangan.',
			'<strong>Woven base fabric.</strong> Kain tenun sebagai rangka. Memberi kekuatan tarik dan menjaga kain tetap rata dan lurus saat digulung ribuan kali, tanpa melipat atau bergelombang.',
			'<strong>Inner water protection.</strong> Lapisan pelindung di sisi yang menghadap jendela — sisi yang paling sering terkena embun dan kelembapan. Lapisan ini yang menahan lembap merambat masuk ke serat kain.',
		)
	);

	$out .= blackroll_content_h( 2, 'Apa efeknya di ruangan' );
	$out .= blackroll_content_list(
		array(
			'<strong>Cahaya.</strong> Bidang kain benar-benar memadamkan cahaya yang melewatinya — ruangan bisa gelap di siang hari. Cocok untuk kamar tidur, kamar bayi, ruang media, dan pekerja shift malam.',
			'<strong>Panas.</strong> Cahaya yang tertahan berarti panas yang tidak masuk. Ruangan menghadap barat terasa jauh lebih sejuk dan beban AC berkurang.',
			'<strong>Privasi.</strong> Dari luar, siluet penghuni tidak terlihat saat lampu menyala di dalam — hal yang tidak bisa diberikan tirai tipis biasa.',
		)
	);

	$out .= blackroll_content_h( 2, 'Satu hal yang perlu diluruskan' );
	$out .= blackroll_content_p(
		'Kain blackout memadamkan cahaya yang <em>melewati kain</em>. Sisa cahaya yang masih terlihat di ruangan biasanya masuk dari celah di sisi kanan-kiri antara kain dan tembok, bukan menembus kainnya. Karena itu, untuk kamar tidur kami menyarankan <strong>pemasangan pada dinding</strong> (di atas kusen), bukan di depan kusen — kain jatuh melewati bidang jendela sehingga celah samping mengecil. Langkahnya ada di <a href="/panduan-pemasangan-roller-blinds/">panduan pemasangan</a>.'
	);

	$out .= blackroll_content_h( 2, 'Kapan blackout bukan jawabannya' );
	$out .= blackroll_content_p(
		'Blackout tepat untuk ruangan yang perlu gelap. Untuk ruangan yang justru ingin tetap terang, pilih material lain:'
	);
	$out .= blackroll_content_list(
		array(
			'<strong>Solar screen</strong> — menahan silau dan panas, ruangan tetap terang dan pandangan ke luar terjaga. Untuk ruang kerja dan kantor.',
			'<strong>Dimout</strong> — meredupkan tanpa menggelapkan total. Jalan tengah untuk ruang keluarga.',
			'<strong>Zebra blinds</strong> — pita transparan dan solid berselang-seling; tingkat cahaya diatur dengan menggeser posisi kain.',
		)
	);
	$out .= blackroll_content_p(
		'Bandingkan kode dan pilihan warnanya di halaman <a href="/material-warna/">Material &amp; Warna</a>.'
	);

	$out .= blackroll_content_h( 2, 'Pertanyaan yang sering ditanyakan' );

	$out .= blackroll_content_h( 3, 'Apakah kain blackout harus berwarna gelap?' );
	$out .= blackroll_content_p(
		'Tidak. Kemampuan menahan cahaya datang dari <em>black out powder layer</em> di bagian tengah kain, bukan dari warna permukaannya. Karena itu kain blackout Blackroll tersedia sampai ke warna-warna terang — seri ML misalnya tetap blackout penuh meski tampil netral dan cerah di dalam ruangan.'
	);

	$out .= blackroll_content_h( 3, 'Apakah ruangan jadi terasa pengap?' );
	$out .= blackroll_content_p(
		'Tidak, karena blinds digulung penuh ke atas saat tidak dipakai — berbeda dengan tirai kain tebal yang tetap menggantung dan menutup sebagian jendela sepanjang hari. Justru sebaliknya: dengan panas matahari yang tertahan, suhu ruangan lebih stabil dan AC tidak bekerja sekeras biasanya.'
	);

	$out .= blackroll_content_h( 3, 'Bagaimana cara membersihkannya?' );
	$out .= blackroll_content_p(
		'Cukup dilap dengan kain lembap; untuk noda gunakan sedikit sabun cair ringan dan usap searah. Jangan direndam, dicuci mesin, atau disikat kasar — lapisan pelindung dan <em>color lock</em> bekerja optimal selama permukaan kain tidak digosok keras.'
	);

	$out .= blackroll_content_h( 3, 'Manual atau motorized untuk kamar?' );
	$out .= blackroll_content_p(
		'Keduanya memakai kain yang sama, jadi hasil pemadaman cahayanya identik. Pilih <a href="/produk/blinds-manual/">manual</a> untuk jendela yang mudah dijangkau, dan <a href="/produk/blinds-motorized/">motorized</a> untuk jendela tinggi, bidang kaca lebar, atau kamar anak — karena tidak ada rantai yang terjangkau.'
	);

	$out .= blackroll_content_cta();

	return trim( $out );
}

/**
 * Article: kitchen blinds.
 *
 * @param callable|null $media Media resolver.
 * @return string
 */
function blackroll_content_article_kitchen( $media = null ) {
	$out = '';

	$out .= blackroll_content_p(
		'Dapur adalah ruangan paling keras untuk sebuah tirai. Uap minyak dari penggorengan menempel dan menjadi lapisan lengket penangkap debu, uap air membuat kain lembap sepanjang hari, dan sisa keduanya berujung pada noda kuning, bau, serta bercak jamur.',
		'{"fontSize":"large"}'
	);
	$out .= blackroll_content_p(
		'Tirai kain biasa menyerap semuanya. Setelah beberapa bulan pilihannya tinggal dua: dicuci berulang sampai warnanya pudar, atau diganti.'
	);

	$out .= blackroll_content_image(
		$media,
		'tech/tahan-air-minyak.webp',
		'Kain roller blinds Blackroll tahan air dan tahan minyak, anti bakteri dan tidak berjamur'
	);

	$out .= blackroll_content_h( 2, 'Kenapa kain berlapis bertahan lebih lama' );
	$out .= blackroll_content_p(
		'Kain Blackroll memiliki lapisan pelindung di permukaannya, sehingga cairan dan minyak <strong>tertahan di atas permukaan</strong>, tidak langsung meresap ke dalam serat. Konsekuensinya sederhana tapi besar untuk dapur:'
	);
	$out .= blackroll_content_list(
		array(
			'<strong>Cipratan tinggal dilap.</strong> Air, kuah, dan percikan minyak dibersihkan dengan lap lembap, tanpa perlu melepas dan mencuci kain.',
			'<strong>Anti bakteri.</strong> Permukaan yang tidak menyerap cairan tidak menyediakan tempat bakteri berkembang biak — penting untuk ruangan tempat makanan diolah.',
			'<strong>Tidak mudah berjamur.</strong> Lembap tidak terperangkap di dalam serat, sehingga bercak hitam jamur tidak tumbuh meski dapur jarang kena matahari langsung.',
			'<strong>Warna tidak cepat menguning.</strong> Minyak yang tidak meresap berarti tidak ada noda yang mengendap permanen di dalam kain.',
		)
	);

	$out .= blackroll_content_h( 2, 'Material mana untuk dapur?' );
	$out .= blackroll_content_p(
		'Sebagian besar dapur ingin tetap terang saat memasak, jadi mulailah dari kebutuhan cahaya, bukan dari warnanya:'
	);
	$out .= blackroll_content_list(
		array(
			'<strong>Solar screen</strong> — pilihan paling umum untuk dapur. Menahan silau dan panas, tetapi cahaya alami tetap masuk sehingga area kerja tidak gelap dan lampu tidak perlu menyala seharian.',
			'<strong>Blackout</strong> — untuk jendela dapur yang menghadap barat dan membuat ruangan panas menyengat di sore hari, atau dapur yang menyatu dengan ruang makan yang ingin bisa digelapkan.',
			'<strong>Zebra blinds</strong> — jika Anda ingin mengatur cahaya sepanjang hari: terang penuh saat memasak, diredupkan saat matahari sore masuk.',
		)
	);

	$out .= blackroll_content_h( 2, 'Pemasangan di dapur: dua catatan' );
	$out .= blackroll_content_list(
		array(
			'<strong>Beri jarak dari kompor.</strong> Jangan pasang blinds tepat di atas atau di samping area api. Tidak ada kain tirai yang dirancang untuk berada dekat sumber api langsung.',
			'<strong>Pilih pemasangan depan kusen.</strong> Di dapur, headrail yang menempel pada kusen lebih mudah dijangkau dan dilap dibanding yang dipasang tinggi di tembok. Langkahnya ada di <a href="/panduan-pemasangan-roller-blinds/">panduan pemasangan</a>.',
		)
	);

	$out .= blackroll_content_h( 2, 'Cara merawatnya' );
	$out .= blackroll_content_list(
		array(
			'Lap permukaan kain dengan kain lembap setiap beberapa minggu — jauh lebih ringan daripada menunggu minyak menumpuk.',
			'Untuk noda minyak, gunakan sedikit sabun cair ringan pada lap, usap searah, lalu lap ulang dengan kain bersih yang lembap.',
			'Jangan direndam, dicuci mesin, atau disikat kasar. Lapisan pelindung bekerja optimal bila permukaannya tidak digosok keras.',
			'Biarkan kain dalam posisi terbuka sampai kering sebelum digulung, agar tidak ada lembap yang terperangkap di gulungan.',
		)
	);

	$out .= blackroll_content_h( 2, 'Pertanyaan yang sering ditanyakan' );

	$out .= blackroll_content_h( 3, 'Apakah kainnya menyerap bau masakan?' );
	$out .= blackroll_content_p(
		'Bau menempel pada kain ketika minyak dan uap meresap ke dalam serat. Karena permukaan kain Blackroll dilapisi pelindung tahan air dan minyak, sebagian besar residu tertahan di permukaan dan ikut terangkat saat dilap — bukan mengendap di dalam kain seperti pada tirai berbahan kain biasa.'
	);

	$out .= blackroll_content_h( 3, 'Bisakah blinds dapur dicuci?' );
	$out .= blackroll_content_p(
		'Tidak perlu, dan sebaiknya tidak. Perawatannya memang dirancang dengan cara dilap, bukan dicuci. Merendam atau mencuci dengan mesin justru merusak lapisan pelindung yang membuat kain tahan minyak sejak awal.'
	);

	$out .= blackroll_content_h( 3, 'Warna apa yang tepat untuk dapur?' );
	$out .= blackroll_content_p(
		'Warna terang membuat dapur terasa lebih lapang dan bersih, tetapi noda lebih cepat terlihat. Warna netral menengah adalah kompromi yang paling awet dipandang. Aksesori White Series cocok untuk dapur bernuansa putih dan kayu terang; Black Series untuk dapur dengan aksen gelap. Lihat pilihannya di <a href="/material-warna/">Material &amp; Warna</a>.'
	);

	$out .= blackroll_content_h( 3, 'Bagaimana dengan jendela kecil di atas sink?' );
	$out .= blackroll_content_p(
		'Bisa. Setiap unit diproduksi sesuai ukuran jendela Anda, termasuk jendela sempit atau memanjang. Yang penting tersedia bidang selebar sekitar 5 cm di kanan dan kiri untuk titik pemasangan clip.'
	);

	$out .= blackroll_content_p(
		'Punya jendela dapur dengan ukuran tidak standar? Setiap unit kami produksi sesuai ukuran jendela Anda — <a href="/kontak/">kirim ukurannya di sini</a>.'
	);

	$out .= blackroll_content_cta();

	return trim( $out );
}

/**
 * Article: choosing blinds per room.
 *
 * @param callable|null $media Media resolver.
 * @return string
 */
function blackroll_content_article_rooms( $media = null ) {
	$out = '';

	$out .= blackroll_content_p(
		'Pertanyaan pertama saat memilih roller blinds bukan "warna apa", melainkan <strong>"ruangan ini butuh cahaya seperti apa?"</strong> Jawabannya menentukan materialnya; warna baru dipilih setelah itu.',
		'{"fontSize":"large"}'
	);
	$out .= blackroll_content_p(
		'Berikut panduan singkat per ruangan, lengkap dengan alasannya.'
	);

	$out .= blackroll_content_h( 2, 'Ruang tamu — tampil rapi, cahaya tetap masuk' );
	$out .= blackroll_content_image(
		$media,
		'lifestyle/ruang-tamu.webp',
		'Roller blinds Blackroll terpasang di ruang tamu bergaya modern'
	);
	$out .= blackroll_content_p(
		'Ruang tamu biasanya ingin terang dan lapang, tetapi tetap terlindung dari silau sore dan pandangan dari luar. <strong>Solar screen</strong> paling pas: silau dan panas tertahan, pandangan ke taman atau jalan tetap terjaga. Bila ruang tamu juga dipakai menonton, pertimbangkan <strong>dimout</strong> agar layar tidak terkena pantulan.'
	);
	$out .= blackroll_content_p(
		'Untuk warna, pilih satu tingkat lebih terang dari dinding agar bidang jendela terasa lebih tinggi. Aksesori White Series menyatu dengan interior terang; Black Series memberi garis tegas pada interior monokrom.'
	);

	$out .= blackroll_content_h( 2, 'Kantor dan ruang kerja — bebas silau di layar' );
	$out .= blackroll_content_image(
		$media,
		'lifestyle/kantor.webp',
		'Roller blinds Blackroll di ruang kerja kantor'
	);
	$out .= blackroll_content_p(
		'Masalah utama ruang kerja adalah silau di layar, bukan kurangnya cahaya. <strong>Solar screen</strong> adalah standarnya: cahaya alami tetap masuk sehingga ruangan tidak terasa pengap, tetapi silau langsung tersaring dan suhu ruangan lebih stabil.'
	);
	$out .= blackroll_content_p(
		'Untuk kantor dengan deretan jendela panjang atau bidang kaca tinggi, <a href="/produk/blinds-motorized/">versi motorized</a> lebih masuk akal — semua unit diatur serempak, tanpa rantai menjuntai di area kerja.'
	);

	$out .= blackroll_content_h( 2, 'Dapur — tahan uap minyak dan lembap' );
	$out .= blackroll_content_image(
		$media,
		'lifestyle/ruang-dapur.webp',
		'Roller blinds Blackroll di area dapur'
	);
	$out .= blackroll_content_p(
		'Dapur menuntut kain yang bisa dilap, bukan dicuci. Lapisan tahan air dan tahan minyak pada kain Blackroll membuat cipratan tidak meresap, dan permukaannya tidak menjadi tempat jamur tumbuh. <strong>Solar screen</strong> untuk dapur yang ingin tetap terang; <strong>blackout</strong> bila jendela menghadap barat dan sore hari terasa menyengat. Pembahasan lengkapnya ada di <a href="/roller-blinds-untuk-dapur/">artikel khusus dapur</a>.'
	);

	$out .= blackroll_content_h( 2, 'Kamar tidur — gelap, sejuk, privat' );
	$out .= blackroll_content_p(
		'Hanya satu jawaban di sini: <strong>blackout</strong>. Lima lapisnya menahan cahaya, panas, sekaligus siluet dari luar saat lampu menyala — <a href="/blackout-5-lapisan-teknologi/">ini penjelasan tiap lapisannya</a>. Untuk hasil paling gelap, pasang pada dinding di atas kusen sehingga kain melewati bidang jendela dan celah samping mengecil.'
	);
	$out .= blackroll_content_p(
		'Untuk kamar anak, pilih <a href="/produk/blinds-motorized/">versi motorized</a> agar tidak ada rantai yang terjangkau anak.'
	);

	$out .= blackroll_content_h( 2, 'Home office — satu ruangan, dua fungsi' );
	$out .= blackroll_content_image(
		$media,
		'lifestyle/ruang-office.webp',
		'Roller blinds Blackroll di ruang home office'
	);
	$out .= blackroll_content_p(
		'Ruangan yang dipakai bekerja pada siang hari dan beristirahat pada malam hari punya dua kebutuhan yang bertentangan. Solusi paling praktis adalah <strong>zebra blinds</strong> — cukup geser posisi kain untuk berpindah dari terang penuh ke redup, tanpa mengganti apa pun. Alternatifnya, <strong>dimout</strong> bila Anda lebih sering butuh keteduhan dibanding cahaya penuh.'
	);
	$out .= blackroll_content_p(
		'Bila di ruangan itu Anda rutin melakukan panggilan video, perhatikan posisi jendela terhadap kamera: jendela di belakang punggung membuat wajah gelap. Blinds yang bisa diturunkan sebagian menyelesaikan masalah ini tanpa perlu memindahkan meja.'
	);

	$out .= blackroll_content_h( 2, 'Apartemen — rapi dan tidak permanen' );
	$out .= blackroll_content_p(
		'Di apartemen, bidang tembok di atas jendela sering kali sempit atau tidak boleh dibor. Untuk kondisi ini pilih <strong>pemasangan depan kusen</strong>: headrail menempel pada kusen, lubang yang dibuat kecil dan mudah ditutup kembali. Roller blinds juga menghemat ruang dibanding gorden bertumpuk — penting untuk unit dengan luas terbatas.'
	);
	$out .= blackroll_content_p(
		'Untuk bidang kaca dari lantai sampai plafon yang umum di apartemen, <a href="/produk/blinds-motorized/">versi motorized</a> menghindarkan Anda dari rantai sepanjang dua meter yang menjuntai di sisi jendela.'
	);

	$out .= blackroll_content_h( 2, 'Ringkasan cepat' );
	$out .= blackroll_content_list(
		array(
			'<strong>Perlu gelap total</strong> — blackout. Kamar tidur, ruang media, kamar bayi.',
			'<strong>Perlu terang tanpa silau</strong> — solar screen. Kantor, ruang kerja, ruang tamu, dapur.',
			'<strong>Perlu redup tapi tidak gelap</strong> — dimout. Ruang keluarga, ruang makan.',
			'<strong>Perlu diatur sepanjang hari</strong> — zebra blinds. Ruangan multifungsi.',
		)
	);
	$out .= blackroll_content_p(
		'Lihat pilihan kain dan kode warnanya di <a href="/material-warna/">Material &amp; Warna</a>, atau lihat hasil pemasangan nyata di <a href="/portofolio/">portofolio</a>.'
	);

	$out .= blackroll_content_cta();

	return trim( $out );
}
