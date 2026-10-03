# Blackroll — Sitemap / Information Architecture

Ini peta halaman resmi Blackroll Blinds (`blackrollblinds.com`), disusun supaya:
1. Client bisa review struktur situs dalam satu pandangan (bukan cuma daftar URL mentah).
2. Jelas mana yang **sengaja publik/ter-index** dan mana yang **sengaja tidak** — supaya
   tidak ada duplicate/thin content yang nyelip ke Google Search Console tanpa disadari.

Struktur di bawah ini mengikuti navigasi yang sudah ada di `themes/blackroll/parts/header.html`
dan `parts/footer.html` — jadi sitemap ini bukan rencana baru, tapi dokumentasi resmi dari
struktur yang sudah dibangun, plus perbaikan kebocoran URL yang tidak sengaja.

## Halaman publik (indexable, ada di sitemap Rank Math)

```
/                               Beranda (front-page)
/tentang-kami/                  Tentang Kami
/produk/                        Produk (index)
  /produk/blinds-manual/        Produk — Blinds Manual
  /produk/blinds-motorized/     Produk — Blinds Motorized
/material-warna/                Material & Warna ([blackroll_selector] — semua shade tampil di sini,
                                 filter seri + material client-side, TIDAK ada halaman per-shade)
/portofolio/                    Portofolio (archive project, [blackroll_portfolio])
  /portofolio/{slug-proyek}/    Detail 1 proyek (single-project.html)
/artikel/                       Artikel (blog index, home.html)
  /artikel/{slug-artikel}/      Detail 1 artikel
  kategori: Panduan / Produk / Inspirasi
/kontak/                        Kontak (form Sebari)
/kebijakan-privasi/             Kebijakan Privasi

(EN — progresif, hanya untuk pasangan yang sudah diterjemahkan Polylang)
/en/...                         Padanan EN dari halaman ID di atas
/en/portfolio/                  Padanan EN dari /portofolio/ (rewrite khusus, rewrites.php)
```

## Sengaja TIDAK publik / TIDAK di-index

Ini yang jadi fokus perbaikan feedback "hindari duplicate content" — bukan halaman yang
dihapus, tapi URL yang sebelumnya otomatis dibuat WordPress padahal tidak dipakai di UX
manapun, dan isinya menduplikasi konten yang sudah tampil di halaman publik di atas.

| URL pattern | Kenapa ada secara default | Kenapa dimatikan |
|---|---|---|
| `/warna/{sku-slug}/` | CPT `shade` otomatis dapat single-page per SKU (24 SKU) | Isinya cuma field meta (SKU, gambar), bukan artikel — semua data ini sudah tampil lengkap di `/material-warna/` lewat selector. Tidak ada link ke sini di manapun. Sekarang: `publicly_queryable => false` → 404. |
| `/seri-warna/black-series/`, `/seri-warna/white-series/` | Taxonomy `color_series` otomatis dapat archive per term | Selector filter series-nya client-side (data attribute), bukan link ke archive taxonomy. Archive-nya cuma me-render ulang shade yang sama. Sekarang: 404. |
| `/tipe-proyek/residensial/`, `/tipe-proyek/kantor/`, `/tipe-proyek/apartemen/` | Taxonomy `project_type` otomatis dapat archive per term | Sama seperti di atas — filter Portofolio client-side, archive taxonomy cuma duplikasi `/portofolio/`. Sekarang: 404. |
| `/kontak/terima-kasih/`, `/kontak/gagal/` | Halaman redirect sukses/gagal form Sebari | Perlu ada (bukan bug) tapi tidak boleh ter-index — sudah `noindex,follow` (`config.php`). |
| URL attachment gambar (`/nama-gambar/`) | WP generate halaman tersendiri utk tiap media | Sudah di-redirect ke file/induk (`security.php`, PATCH-15). |
| Author archive, date archive (`/author/...`, `/2026/09/...`) | Default WP untuk semua situs | Situs ini 1 author ("Tim Blackroll") dan bukan blog kronologis — archive ini cuma duplikat `/artikel/`. *(Belum ada penanganan eksplisit di kode — dicatat sebagai item lanjutan, lihat catatan di bawah.)* |
| Halaman search (`/?s=...`) | Fitur search aktif (`search.html`) | PATCH-09 (PRD Amendment 01) sebenarnya me-lock default "search disabled" persis karena alasan ini (thin/duplicate content) — situs ini shipped dengan search aktif sebagai override. Fitur tetap dipertahankan (berguna untuk visitor), tapi sekarang `noindex,follow` (`config.php`, `is_search()`) supaya tidak jadi risiko SEO yang di-flag PATCH-09. |

**Item lanjutan (belum dikerjakan di revisi ini, perlu keputusan/waktu terpisah):**
author archive & date archive belum secara eksplisit di-redirect/noindex di kode. Karena
situs ini 1-author, low-risk untuk Google (tidak banyak yang link ke situ), tapi kalau mau
tuntas: tambahkan `noindex` di `wp_head` untuk `is_author()`/`is_date()` seperti pola yang
sudah dipakai untuk halaman thank-you/gagal di `plugins/blackroll-core/inc/config.php`.

## Cara verifikasi setelah deploy
1. `wp rewrite flush` di server (wajib — tanpa ini, rule lama masih di-cache).
2. Buka `/warna/{salah-satu-sku}/`, `/seri-warna/black-series/`, `/tipe-proyek/kantor/` →
   harus 404.
3. Cek Rank Math → Sitemap: hanya URL di tabel "Halaman publik" di atas yang muncul.
4. Search Console → Coverage: setelah beberapa hari crawl ulang, URL yang di-nonaktifkan
   di atas harusnya hilang dari "Duplicate" / "Crawled - not indexed" (butuh waktu, bukan
   instan).
