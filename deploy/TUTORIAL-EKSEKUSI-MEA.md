# Tutorial Eksekusi — Blackroll Blinds (untuk Tim MEA)

Dokumen ini adalah **jalur eksekusi tunggal**: baca dari atas ke bawah, ikuti urutannya. Detail
lebih dalam tiap topik ada di dokumen lain (ditautkan di tiap bagian) — tidak perlu dibaca semua
dulu, cukup dibuka kalau dirujuk.

> **Konteks penting:** `blackrollblinds.com` **sudah live**. Ini bukan launch pertama — ini
> **cutover** dari versi yang sedang jalan di server ke revisi terbaru di repo (perbaikan
> duplicate-content/SEO, motion premium, dan beberapa dokumentasi). Perlakukan seperti update
> produksi: backup dulu, uji di staging kalau ada, jangan buru-buru di jam trafik tinggi.

---

## 0. Ringkasan status (per 2026-09)

Semua keputusan desain sudah locked & sudah dicek cocok dengan kode (Amendment 01, 18 patch,
D1–D6) — **tidak ada lagi keputusan produk yang mengambang**. Yang berubah baru-baru ini:

| Item | Status |
|---|---|
| Konfirmasi Sebari `redirect_url` (Open Dependency #7) | ✅ **Selesai** — Sebari konfirmasi didukung. Mode R (redirect) jalan sesuai default kode, tidak perlu ubah apa pun. |
| Kebijakan Privasi — sign-off legal | ✅ **Selesai.** Naskah final **belum di-upload** — draft yang ada di `plugins/blackroll-core/inc/seed-content.php` masih dipakai sampai file final masuk. |
| Fix duplicate-content (shade/taxonomy URL publik yang tidak sengaja) | ✅ Selesai di kode (`main`) |
| Motion premium (video hero, Three.js, Lottie) | ✅ Selesai di kode (`main`) |
| Search page noindex | ✅ Selesai di kode (`main`) |
| Fase 0: bug situs live (CTA 404, layout sempit, selector, mobile) + foto koleksi Sept 2026 | ✅ Selesai di kode (`main`) — lihat `docs/PLAN-REVISI-PREMIUM.md` |
| Logo & favicon resmi | ✅ **Selesai** — di-trace dari Company Profile PDF klien |
| Terjemahan EN | ⏳ Progresif by design (D6) — bukan blocker |

**Kode yang akan di-deploy ada di branch `main`** di `github.com/MEAgrup/Blackroll`
(berisi semua revisi di atas, termasuk Fase 0). Branch `claude/*` lama hanya arsip.

Ikuti **`deploy/HOSTINGER-SSH.md` Bagian 0–2**, dengan satu koreksi penting: dokumen itu sudah
diperbaiki di revisi ini supaya menunjuk ke branch yang benar. Ringkasnya:

```bash
cd ~/src/blackroll   # kalau belum pernah clone, lihat HOSTINGER-SSH.md Bagian 1
git fetch origin
git checkout main
git pull origin main

rsync -a --delete ~/src/blackroll/themes/blackroll/       "$WPROOT/wp-content/themes/blackroll/"
rsync -a --delete ~/src/blackroll/plugins/blackroll-core/ "$WPROOT/wp-content/plugins/blackroll-core/"

cd "$WPROOT"
wp theme activate blackroll
wp plugin activate blackroll-core
```

**Wajib setelah rsync di atas** (branch ini mengubah beberapa URL jadi non-publik — lihat
Bagian 5):

```bash
wp rewrite flush --hard
```

Kalau ini instalasi **pertama kali** (bukan update situs yang sudah ada), lanjutkan penuh ke
`deploy/HOSTINGER-SSH.md` Bagian 3–6 (install 4 plugin pendukung, baseline WP, `wp blackroll seed`
+ `wp blackroll content`) sebelum lanjut ke Langkah 4 di bawah.

---

## 4. Konten: Kebijakan Privasi

Sign-off legal sudah selesai, tapi **naskah final belum di-upload ke sesi ini**. Dua opsi:

- **Kalau naskah final sudah siap:** ganti isi fungsi terkait Kebijakan Privasi di
  `plugins/blackroll-core/inc/seed-content.php`, commit ke branch ini, `git pull` di server, lalu:
  ```bash
  wp blackroll content --force   # --force karena draft lama dianggap "sudah ada isi"
  ```
- **Kalau belum:** draft yang ada tetap tayang (bukan halaman kosong/rusak) — aman untuk go-live,
  tinggal diganti kapan pun naskah final masuk, tanpa migrasi khusus.

---

## 5. SEO & sitemap — verifikasi fix duplicate-content

Ini bagian paling penting untuk dicek manual, karena inilah alasan revisi ini dibuat.

1. Buka `deploy/SITEMAP.md` — itu daftar resmi halaman yang seharusnya publik.
2. Setelah `wp rewrite flush --hard` (Langkah 3), tes manual di browser — **semua harus 404**:
   - `https://blackrollblinds.com/warna/{slug-sku-apa-saja}/`
   - `https://blackrollblinds.com/seri-warna/black-series/`
   - `https://blackrollblinds.com/tipe-proyek/kantor/`
3. Tes halaman search **harus tetap bisa dipakai** tapi cek `view-source:` mengandung
   `<meta name="robots" content="noindex,follow">`:
   - `https://blackrollblinds.com/?s=blinds`
4. Rank Math → Sitemap: bandingkan isinya dengan tabel "Halaman publik" di `deploy/SITEMAP.md` —
   tidak boleh ada URL di luar daftar itu.
5. Google Search Console: submit ulang sitemap (`/sitemap_index.xml`). Efek pembersihan
   "Duplicate"/"Crawled — not indexed" di Coverage butuh beberapa hari, bukan instan.

---

## 6. Sebari, SEO wizard, keamanan — checklist singkat

Detail lengkap tiap poin ada di `deploy/DEPLOY.md` (Bagian 4–6) dan `deploy/HOSTINGER-SSH.md`
(Bagian 7–8). Sudah tidak ada open question di sini — tinggal eksekusi:

- [ ] **Sebari dashboard** (Open Dep #7 sudah closed, tinggal set): form `971` → redirect sukses
      ke `/kontak/terima-kasih/`, redirect gagal ke `/kontak/gagal/`. Test 1 submit end-to-end,
      pastikan notifikasi WhatsApp masuk.
- [ ] **Rank Math**: jalankan setup wizard, isi title/meta per halaman, verifikasi schema
      (Organization, LocalBusiness, Product, Article, BreadcrumbList) valid di Rich Results Test.
- [ ] **Keamanan**: `deploy/uploads.htaccess` sudah di-copy ke `wp-content/uploads/.htaccess`;
      `WP_ENVIRONMENT_TYPE=production` sudah di-set (supaya `robots.txt` **tidak** lagi
      `Disallow: /`); setelah Sebari+font+peta terbukti jalan, flip CSP ke enforce
      (`BLACKROLL_CSP_REPORT_ONLY=false`).

---

## 7. Motion premium (video/Three.js/Lottie) — cek tampilan, bukan cuma fungsi

Ini fitur baru di revisi ini. Karena aset asli (video, model 3D, animasi) **belum ada dari Tim
Rollerblind**, yang harus dicek bukan "apakah videonya bagus" tapi "apakah fallback-nya jalan
dengan benar" — supaya situs tidak terlihat rusak sebelum aset asli datang:

- [ ] Homepage: hero tampil normal (teks H1 + paragraf + tombol) tanpa video — ini **memang**
      perilaku default sampai file video diisi lewat `blackroll_hero_video_url()`
      (`plugins/blackroll-core/inc/config.php`). Bukan bug.
- [ ] Halaman Produk (`/produk/blinds-manual/`, `/produk/blinds-motorized/`): ada kotak
      "Pratinjau 3D interaktif — tampil di perangkat yang mendukung" — ini fallback teks yang
      benar selama belum ada `npm run build` di server (lihat catatan di bawah) atau di
      perangkat tanpa WebGL/koneksi lambat/`prefers-reduced-motion`.
- [ ] **Kalau ingin animasi/3D-nya benar-benar tampil** (bukan cuma fallback): jalankan
      `npm install && npm run build` di dalam folder repo sebelum `rsync` di Langkah 3, supaya
      `themes/blackroll/assets/js/dist/` ikut ter-generate dan ter-copy ke server. Tanpa ini,
      situs tetap berfungsi penuh (fallback teks/gambar statis tampil), cuma belum ada animasinya.

---

## 8. Verifikasi akhir sebelum bilang "selesai"

Checklist lengkap ada di `deploy/QA-CHECKLIST.md` — jalankan semua bagian, terutama:
- **SEO** (termasuk 2 item baru: search noindex, 404 pada URL shade/taxonomy)
- **Functional** (submit Sebari, selector, portfolio, floating CTA)
- **Content** (tidak ada teks "(konten contoh)" tersisa)

```bash
wp blackroll doctor    # cek cepat kalau ada gambar yang tidak muncul
curl -s https://blackrollblinds.com/robots.txt   # pastikan TIDAK ada "Disallow: /"
```

---

## 9. Setelah live

- Purge cache LiteSpeed sekali lagi (`wp litespeed-purge all`).
- Catat di ops runbook (`deploy/DEPLOY.md` Bagian 8) siapa pemegang akses admin, dan siapa
  **owner pengecekan update bulanan** (wajib diisi nama, bukan dibiarkan kosong).
- Pantau Search Console selama ±1 minggu untuk konfirmasi URL duplicate hilang dari Coverage.
- Kalau naskah final Kebijakan Privasi atau aset logo/video dari Tim Rollerblind sudah masuk,
  kembali ke Langkah 4/7 — tidak perlu ulangi seluruh proses ini dari awal, cukup bagian yang
  relevan.

---

## Peta dokumen (kalau butuh detail lebih)

| Dokumen | Isinya |
|---|---|
| `deploy/DEPLOY.md` | Keputusan & alasan tiap langkah ops (bukan urutan perintah) |
| `deploy/HOSTINGER-SSH.md` | Perintah SSH/WP-CLI copy-paste, lengkap dengan troubleshooting gambar |
| `deploy/SITEMAP.md` | Daftar resmi halaman publik vs. yang sengaja tidak publik |
| `deploy/QA-CHECKLIST.md` | Checklist lengkap performa/SEO/a11y/keamanan/konten/fungsional |
| `README.md` | Arsitektur kode, cara tambah shade/project/artikel, status tiap placeholder |
