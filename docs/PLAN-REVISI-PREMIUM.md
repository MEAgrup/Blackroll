# Plan Revisi Blackroll: Tampilan Premium + Hero Three.js

Permintaan klien (Sept 2026): tampilan website **lebih premium**, homepage memakai
**hero video/Three.js**, dan koleksi baru dimasukkan (folder Drive "Blackroll",
19 zip, 431 foto, tanpa video).

Dokumen ini adalah sumber acuan plan dan status. Perbarui kolom status setiap
kali satu fase selesai.

| Fase | Isi | Status |
|---|---|---|
| 0 | Perbaikan bug situs live, logo resmi, kurasi foto | ✅ Selesai ([PR #2](https://github.com/MEAgrup/Blackroll/pull/2)) |
| 1 | Visual premium: layout, tipografi, motion | ⏳ Siap dikerjakan (desain diserahkan ke MEA) |
| 2 | Hero homepage Three.js "The Blind Reveal" | ⏳ Bisa jalan tanpa klien |
| 3 | Koleksi (Roller utama, Zebra pelengkap) + susunan homepage baru | ⏳ Siap dikerjakan (pemetaan SKU sudah ada) |
| 4 | QA + deploy ke Hostinger | ⏳ |

---

## Temuan audit blackrollblinds.com (2026-09-28)

1. Versi live adalah branch lama (`content`); motion/Three.js belum aktif.
2. Tidak ada logo; header/footer menampilkan teks domain.
3. Semua section berbentuk kotak sempit ~760px; hero hanya kotak hitam.
4. Link di section gelap tidak terbaca (hitam di atas hitam).
5. Selector Material & Warna: preview kosong, 7 dari 8 swatch blank.
6. Halaman produk tanpa foto.
7. Footer "Peta Situs" menampilkan halaman sistem.
8. Mobile: teks menempel ke tepi layar, tombol CTA mengambang menutupi konten.
9. **Tombol CTA di semua halaman dan redirect form Sebari mengarah ke `/en/contact/` (404)**
   karena Polylang di production hanya punya bahasa English.

Semua poin di atas sudah diperbaiki di kode pada Fase 0. Poin 9 masih butuh
tindakan di wp-admin (lihat `deploy/DEPLOY.md` §2).

## Aset dari klien

- **Roller blind:** RBXL A004 dan folder `blind 2–13, 17–19`. Warna: putih,
  off-white, cream/beige, abu muda, biru muda, charcoal, taupe, linen abu gelap.
- **Zebra / combi blind:** `XLS004`, `blind 14`, `blind 16`.
- Hasil kurasi (80 WebP) ada di `themes/blackroll/assets/images/collection/`,
  dengan asal tiap file dicatat di README folder itu.
- Catatan:
  - Foto asli memperlihatkan tulisan "Ordinary Furnishings" (toko lain) di dinding;
    semua hasil kurasi sudah di-crop.
  - Tidak ada video dan tidak ada foto suasana ruangan.
  - Hanya `A004` dan `S004` yang bisa dipetakan ke SKU dari nama folder.

---

## Fase 0: Perbaikan dasar ✅

- Perbaikan bug 2–9 di atas.
- Logo resmi: awalnya di-trace dari Company Profile PDF, lalu diganti dengan trace
  dari PNG resmi klien (`logo-horizontal.svg`, `logo-stacked.svg`, `logo-full.svg`
  dengan tagline, `favicon.svg`).
- Kurasi foto: foto depan, foto tergulung, detail, swatch, dan tekstur kain per warna.
- Galeri foto di halaman Manual dan Motorized; baris "ruangan" di homepage
  memakai foto produk asli.

## Fase 1: Visual premium

- **Layout:** full-bleed, lebar maksimal 1440px, ruang kosong lebih lega, satu
  grid yang konsisten, garis tipis, sudut tajam.
- **Palet:** tetap hitam-putih, ditambah netral hangat (stone/sand, mis. `#EDE9E3`)
  untuk latar section dan satu aksen metalik tipis.
- **Motion:** smooth scroll, teks/foto muncul bertahap saat di-scroll, efek hover
  pada kartu. Semua mati otomatis kalau pengunjung memilih `prefers-reduced-motion`.
- **Tipografi:** serif elegan untuk judul besar (mis. Fraunces/Cormorant),
  Anton untuk label kecil. D5 (Anton) boleh diubah: klien menyerahkan desain ke MEA.

## Fase 2: Hero Three.js "The Blind Reveal"

- Hero satu layar penuh. Kanvas WebGL berisi **kain blind 3D** dengan tekstur
  dari foto close-up klien.
  - Saat halaman dibuka, blind turun perlahan.
  - Saat scroll, blind **tergulung naik mengikuti scroll** dan memperlihatkan
    foto di belakangnya.
- **Pemilih warna:** chip warna di hero; kalau diklik, kain 3D berganti dengan
  transisi halus.
- **Toggle Roller ↔ Zebra:** model berganti; garis zebra bisa bergeser.
- H1 dan CTA tetap teks HTML di atas kanvas (SEO dan LCP aman).
- **Fallback:** reduced-motion, tanpa WebGL, atau koneksi lambat/hemat data
  → foto statis.
- **Teknis:**
  - Entry baru `home-hero-3d.js`, dimuat setelah halaman tampil.
  - Gate `build:analyze` diperluas: three.js boleh ada di chunk Product dan
    hero homepage, tidak di tempat lain.
  - Target: bundle three.js < 200KB gzip, DPR maksimal 1.5 di mobile.
- Placeholder bilah hitam di halaman Produk diganti memakai scene yang sama.
- Slot video hero yang sudah ada tetap disimpan untuk footage di masa depan.

## Fase 3: Koleksi dan susunan homepage

- Halaman **Koleksi Roller Blind** dan **Koleksi Zebra Blind**: grid warna dan
  galeri detail.
- Selector warna diisi foto baru; material Zebra ditampilkan.
- Urutan homepage:
  1. Hero
  2. Angka kepercayaan
  3. Koleksi (Roller / Zebra)
  4. Detail kualitas (bracket, bottom bar, rantai)
  5. Solusi per ruangan (tabel dari dokumen landing page klien)
  6. Manual vs Motorized
  7. Portofolio
  8. Proses (Konsultasi → Ukur → Pasang)
  9. CTA WhatsApp
- Taksonomi `collection` di plugin supaya tim konten bisa menambah koleksi
  dari wp-admin.

## Fase 4: QA dan deploy

- Uji perangkat nyata (terutama Android kelas menengah), fallback, aksesibilitas,
  dan LCP < 3 detik (trade-off yang sudah diterima klien).
- Deploy dari `main` ikut `deploy/TUTORIAL-EKSEKUSI-MEA.md`.

---

## Jawaban klien (Hendrik Limas, WhatsApp, 2026-10-01)

| Pertanyaan | Jawaban | Dampak ke plan |
|---|---|---|
| Arah website | Bisnis Blackroll sekarang fokus **offline** (proyek residence & commercial). Website **untuk pengenalan produk**. "Bebas, atur aja, asal website jalan." | Website = katalog produk + bukti proyek + konsultasi WhatsApp. Tidak perlu fitur jualan online. Keputusan desain diserahkan ke tim MEA. |
| Tulisan "Ordinary Furnishings" di foto | Tidak usah ada tulisannya. | ✅ Sudah: semua foto kurasi di-crop. |
| Logo resmi SVG/PNG | Tidak ada SVG; klien kirim **PNG putih transparan** (dengan tagline "BLACK IS COOL"). | ✅ Sudah: SVG di-trace ulang dari PNG ini (`assets/images/brand/`). |
| Zebra jadi lini utama? | **Roller blind yang utama.** | Homepage dan hero fokus Roller; Zebra tampil sebagai lini pelengkap. |
| Foto suasana ruangan / video | Bebas. | Hero Three.js dibangun dari foto yang ada; tidak menunggu video. |

Karena desain diserahkan ke MEA, **keputusan font (D5) diambil tim MEA di Fase 1**.

## Jawaban klien lanjutan (2026-10-02)

| Pertanyaan | Jawaban | Status |
|---|---|---|
| Pemetaan folder `blind N` → SKU | "Tentukan saja dulu" | ✅ Pemetaan sementara oleh MEA di `blackroll_shade_photo_keys()`; tabel di `themes/blackroll/assets/images/collection/README.md`. Semua 24 SKU sekarang punya foto. |
| Foto motor/remote | Tidak ada; cukup foto manual | ✅ Galeri Motorized tetap memakai foto kain/hardware tanpa rantai. |
| Klaim ">100 proyek", "ready stock" | Boleh ditampilkan | ✅ "100+" sudah ada; "ready stock" ditambahkan di section kepercayaan homepage. |
| Instagram resmi | `@blackroll.official` | ✅ Footer, blok kontak cadangan, dan schema diganti. |
| Daftar proyek untuk Portofolio | Menyusul | ⏳ Portofolio tetap memakai data lama sampai daftar proyek masuk. |

## Perlu dikerjakan tim MEA di wp-admin production

- Polylang: tambahkan **Bahasa Indonesia**, jadikan default, assign ulang konten.
- Site Editor: Reset template part Header/Footer kalau pernah diedit, supaya
  logo dan footer baru yang dipakai.
