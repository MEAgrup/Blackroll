# Blackroll — Deploy & isi konten via SSH Hostinger

Runbook copy-paste untuk mengangkat repo ini ke WordPress di Hostinger, lalu mengisi
seluruh konten dengan satu perintah. Untuk konteks keputusan & ops jangka panjang,
baca `deploy/DEPLOY.md`; file ini murni urutan perintah.

> Jalankan di **staging** dulu bila environment staging Hostinger sudah dibuat.
> Semua perintah aman diulang (idempotent) kecuali yang ditandai ⚠️.

---

## 0. Masuk dan temukan root WordPress

```bash
ssh u123456789@<ip-atau-host> -p 65002        # port SSH Hostinger biasanya 65002
cd ~/domains/blackrollblinds.com/public_html  # atau ~/public_html — cek dengan ls ~
ls -d wp-content wp-config.php                # keduanya harus ada di sini
```

Simpan path itu sebagai variabel supaya sisa perintah bisa ditempel apa adanya:

```bash
export WPROOT="$PWD"
```

Cek WP-CLI (Hostinger menyediakannya secara default):

```bash
wp --info --path="$WPROOT"
```

Kalau `wp` tidak ditemukan:

```bash
curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar && mkdir -p ~/bin && mv wp-cli.phar ~/bin/wp
export PATH="$HOME/bin:$PATH"        # tambahkan juga ke ~/.bashrc
```

---

## 1. Ambil kode dari GitHub

Clone repo ke luar `public_html` supaya file `.git`, `README.md`, dan `package.json`
tidak pernah bisa diakses publik:

```bash
mkdir -p ~/src && cd ~/src
git clone https://github.com/MEAgrup/Blackroll.git blackroll
cd blackroll
git checkout claude/blackroll-blinds-revisi-ij6kuj
```

Pembaruan berikutnya cukup:

```bash
cd ~/src/blackroll && git pull origin claude/blackroll-blinds-revisi-ij6kuj
```

---

## 2. Salin theme + plugin ke WordPress

```bash
rsync -a --delete ~/src/blackroll/themes/blackroll/          "$WPROOT/wp-content/themes/blackroll/"
rsync -a --delete ~/src/blackroll/plugins/blackroll-core/    "$WPROOT/wp-content/plugins/blackroll-core/"
```

`--delete` membuat folder di server persis sama dengan repo — jangan pernah mengedit
file theme/plugin langsung di server, karena akan tertimpa pada sinkronisasi berikutnya.

> **Kalau ini update dari revisi sebelumnya (bukan install pertama):** branch ini mengubah
> registrasi CPT `shade` dan taxonomy `color_series`/`project_type` jadi non-publik (fix
> duplicate content — lihat `deploy/SITEMAP.md`). Rewrite rule lama tetap ter-cache sampai
> di-flush, jadi setelah `rsync` di atas **wajib** jalankan:
> ```bash
> wp rewrite flush --hard
> ```
> lalu verifikasi `/warna/{sku}/`, `/seri-warna/*`, `/tipe-proyek/*` benar-benar 404 (lihat
> `deploy/QA-CHECKLIST.md`).

Aktifkan:

```bash
cd "$WPROOT"
wp theme activate blackroll
wp plugin activate blackroll-core
```

---

## 3. Plugin pendukung (4 buah, sesuai Module 11 Rule 4)

```bash
wp plugin install polylang rank-math-seo better-wp-security litespeed-cache --activate
```

`better-wp-security` = Solid Security Basic. Tidak ada SCF (keputusan D3 — field pakai
`register_post_meta` bawaan). Setelah ini, jangan menambah plugin tanpa alasan kuat.

---

## 4. Baseline WordPress

```bash
wp option update timezone_string 'Asia/Jakarta'
wp language core install id_ID && wp site switch-language id_ID
wp rewrite structure '/%postname%/' --hard
wp rewrite flush --hard
```

---

## 5. Isi konten — dua perintah

```bash
wp blackroll seed        # halaman inti, 24 SKU shade, portofolio, artikel, sideload foto
wp blackroll content     # tulis copy Bahasa Indonesia final ke halaman & artikel
```

Yang dihasilkan `wp blackroll seed`:

| Item | Detail |
|---|---|
| Halaman | Beranda, Tentang Kami, Produk (+ Blinds Manual, Blinds Motorized), Material & Warna, Kontak, Terima Kasih, Pengiriman Gagal, Kebijakan Privasi, Artikel |
| Shade | 24 SKU dari sheet klien, lengkap seri warna + material, foto untuk yang tersedia |
| Portofolio | 4 proyek (3 di antaranya *featured* untuk homepage) |
| Artikel | 4 artikel siap tayang di kategori Panduan / Produk / Inspirasi |
| Media | Foto produk, lifestyle, dan materi teknis dari `seed-assets/` (WebP + alt Bahasa Indonesia) |

`wp blackroll content` menulis naskah panjangnya: **Tentang Kami**, **Kebijakan Privasi**,
dan isi keempat artikel. Perintah ini **tidak menimpa halaman yang sudah diedit orang** —
kalau isinya bukan lagi placeholder, ia melewatinya dan memberi tahu Anda.

```bash
wp blackroll content --dry-run    # lihat apa yang akan berubah, tanpa menulis
wp blackroll content --force      # ⚠️ timpa juga yang sudah diedit editor
```

Sesudah menarik commit baru dari GitHub, jalankan ulang `wp blackroll content` untuk
menyalurkan revisi naskah ke database.

Terakhir, set halaman depan dan halaman artikel (seed sudah melakukannya, ini verifikasi):

```bash
wp option get show_on_front      # harus: page
wp post list --post_type=page --fields=ID,post_name,post_status
```

---

## 6. Verifikasi cepat

Kalau ada gambar yang tidak muncul, jalankan ini dulu — ia menelusuri seluruh jalur
gambar (theme asset → URL situs → folder uploads → dukungan WebP → Media Library →
cakupan per konten) dan menyebutkan perbaikannya:

```bash
wp blackroll doctor
```

Penyebab paling sering, sesuai urutan yang diperiksa perintah itu:

| Gejala | Penyebab | Perbaikan |
|---|---|---|
| Semua gambar konten kosong, Media Library kosong | `wp blackroll seed` belum jalan | `wp blackroll seed` |
| Seed "sukses" tapi Media Library tetap kosong | PHP tanpa dukungan WebP → sideload gagal diam-diam | aktifkan WebP di GD (hPanel → PHP Configuration), lalu seed ulang |
| Gambar tema (foto ruangan) 404 | folder `assets/images/` tidak ikut ter-upload | ulangi `rsync` di langkah 2 |
| Gambar 403 | `.htaccess` di `uploads/` memblokir semua, bukan hanya PHP | pakai ulang `deploy/uploads.htaccess` |
| Gambar diblokir browser | `siteurl` masih `http://` di halaman https | `wp search-replace` ke https |
| Halaman lama masih tampil | cache LiteSpeed | `wp litespeed-purge all` |

### Verifikasi manual

```bash
wp post list --post_type=post  --fields=ID,post_name,post_status
wp post list --post_type=shade --format=count      # harus 24
wp post list --post_type=project --format=count    # harus 4
wp media list --format=count
curl -sI https://blackrollblinds.com/ | head -1
```

Buka manual: `/`, `/tentang-kami/`, `/produk/blinds-manual/`, `/material-warna/`,
`/portofolio/`, `/artikel/`, `/kontak/`, `/kebijakan-privasi/`.

---

## 7. Form Sebari (Open Dependency #7 — ✅ sudah dikonfirmasi Bisa, 2026-09)

Tim Sebari sudah konfirmasi `redirect_url` didukung — Mode R (redirect) jalan sesuai default
kode, tidak perlu ganti apa pun. Tinggal login ke dashboard Sebari, buka form `971`, isi redirect:

- Sukses → `https://blackrollblinds.com/kontak/terima-kasih/`
- Gagal → `https://blackrollblinds.com/kontak/gagal/`

(Cadangan saja, tidak diperkirakan perlu) Kalau suatu saat redirect Sebari berhenti berfungsi:

```bash
wp config set BLACKROLL_SEBARI_SUBMIT_MODE fetch --type=constant
```

Lalu kirim satu submit uji dan pastikan notifikasi WhatsApp masuk.

---

## 8. Hardening

```bash
cp ~/src/blackroll/deploy/uploads.htaccess "$WPROOT/wp-content/uploads/.htaccess"
wp config set DISALLOW_FILE_EDIT true --raw --type=constant
wp config set WP_ENVIRONMENT_TYPE production --type=constant   # ⚠️ hanya di produksi
```

Isi `deploy/security-headers.htaccess` ditambahkan ke `.htaccess` root bila ingin HSTS.
CSP sudah dikirim oleh plugin dalam mode *report-only* — setelah submit Sebari, font,
dan peta terbukti jalan, baru dikunci:

```bash
wp config set BLACKROLL_CSP_REPORT_ONLY false --raw --type=constant
```

---

## 9. Go-live

```bash
wp option get siteurl                               # WAJIB sudah https:// sebelum apa pun
wp search-replace 'https://staging.blackrollblinds.com' 'https://blackrollblinds.com' --skip-columns=guid --precise
wp litespeed-purge all
wp rewrite flush --hard
wp blackroll doctor                                 # tahap 8 memastikan gambar benar-benar terkirim
curl -s https://blackrollblinds.com/robots.txt      # pastikan TIDAK ada "Disallow: /"
```

> **Pelajaran dari go-live pertama:** `siteurl`/`home` yang masih `http://` sementara
> server menjawab di `https://` membuat **seluruh** gambar diblokir browser sebagai
> mixed content, walaupun setiap file dan record database sudah benar. Selalu set
> `siteurl`/`home` ke https lebih dulu, baru `search-replace`, baru purge cache.
> Sejak itu gambar di dalam naskah artikel ditulis sebagai URL root-relative
> (`/wp-content/uploads/...`), jadi perpindahan domain atau protokol berikutnya
> tidak lagi menyeret URL lama ikut serta.

Lalu di Search Console: submit sitemap Rank Math (`/sitemap_index.xml`).

---

## Yang masih menunggu dari klien

| Item | Status | Dampak bila belum ada |
|---|---|---|
| Logo & favicon vektor resmi | ⏳ masih menunggu (Tim Rollerblind) | Saat ini memakai rekonstruksi placeholder |
| Review legal Kebijakan Privasi | ✅ **sign-off selesai (2026-09)** | Naskah final belum di-upload — draft di `seed-content.php` masih dipakai sampai file final masuk, lalu jalankan ulang `wp blackroll content --force` untuk halaman itu saja |
| Alamat & jam operasional final | ⏳ | Schema LocalBusiness memakai data dari Settings → Blackroll |
| Terjemahan EN | ⏳ progresif (by design, D6) | Halaman ID sudah lengkap, bukan blocker |
