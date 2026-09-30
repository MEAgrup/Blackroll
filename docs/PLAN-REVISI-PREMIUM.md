# Plan Revisi Blackroll: Tampilan Premium + Hero Three.js

Permintaan klien (Sept 2026): tampilan website **lebih premium**, homepage memakai
**hero video/Three.js**, dan koleksi baru dimasukkan (folder Drive "Blackroll",
19 zip, 431 foto, tanpa video).

Dokumen ini adalah sumber acuan plan dan status. Perbarui kolom status setiap
kali satu fase selesai.

| Fase | Isi | Status |
|---|---|---|
| 0 | Perbaikan bug situs live, logo resmi, kurasi foto | ✅ Selesai ([PR #2](https://github.com/MEAgrup/Blackroll/pull/2)) |
| 1 | Visual premium: layout, tipografi, motion | ⏳ Sebagian bisa jalan; font menunggu klien |
| 2 | Hero homepage Three.js "The Blind Reveal" | ⏳ Bisa jalan tanpa klien |
| 3 | Koleksi (Roller & Zebra) + susunan homepage baru | ⏳ Butuh pemetaan SKU dari klien |
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
- Logo resmi di-trace dari Company Profile PDF (`logo-horizontal.svg`,
  `logo-stacked.svg`, `favicon.svg`).
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
- **Tipografi (butuh keputusan klien):** serif elegan untuk judul besar
  (mis. Fraunces/Cormorant), Anton untuk label kecil. Anton dikunci di
  keputusan D5, jadi perubahan ini harus disetujui klien.

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

## Menunggu dari klien

1. **Pemetaan folder `blind 2…19` ke kode SKU.** Tambahkan ke
   `blackroll_shade_photo_keys()` di `plugins/blackroll-core/inc/selector.php`.
2. **Font judul:** tetap Anton atau ganti ke serif premium?
3. **Produk Zebra:** jadi lini utama di samping Roller? **Outdoor:** kategori
   sendiri atau material?
4. **Foto motor/remote** untuk halaman Motorized, foto suasana ruangan, dan
   video hero (opsional).
5. **Angka klaim** (">100 proyek", "ready stock"): boleh ditampilkan?
6. Instagram resmi: `@blackroll.blinds` (footer) atau `@blackroll.official`
   (dokumen landing page)?

## Perlu dikerjakan tim MEA di wp-admin production

- Polylang: tambahkan **Bahasa Indonesia**, jadikan default, assign ulang konten.
- Site Editor: Reset template part Header/Footer kalau pernah diedit, supaya
  logo dan footer baru yang dipakai.
