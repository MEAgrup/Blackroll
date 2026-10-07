# Panduan Mengelola Website Blackroll (wp-admin)

Panduan ini untuk tim Blackroll yang akan mengelola isi website **blackrollblinds.com**
sehari-hari. Semua langkah dikerjakan dari **wp-admin** (dashboard WordPress) lewat
browser — tidak perlu menyentuh kode atau hosting.

**Isi panduan**

1. [Masuk ke wp-admin & mengenal menu](#1-masuk-ke-wp-admin--mengenal-menu)
2. [Aturan aman (wajib dibaca)](#2-aturan-aman-wajib-dibaca)
3. [Mengunggah gambar (Media)](#3-mengunggah-gambar-media)
4. [Menambah & mengedit warna / SKU produk (Shades)](#4-menambah--mengedit-warna--sku-produk-shades)
5. [Mengelola Seri Warna (Black Series / White Series)](#5-mengelola-seri-warna)
6. [Mengganti nomor WhatsApp, Instagram, TikTok & alamat](#6-mengganti-nomor-whatsapp-instagram-tiktok--alamat)
7. [Menambah & mengedit artikel](#7-menambah--mengedit-artikel)
8. [Menambah proyek Portofolio](#8-menambah-proyek-portofolio)
9. [Mengedit halaman Tentang Kami & Kebijakan Privasi](#9-mengedit-halaman-tentang-kami--kebijakan-privasi)
10. [Mengubah menu navigasi](#10-mengubah-menu-navigasi)
11. [Membersihkan cache (setelah setiap perubahan)](#11-membersihkan-cache)
12. [Mengelola pengguna & password](#12-mengelola-pengguna--password)
13. [Data dari form Kontak](#13-data-dari-form-kontak)
14. [Perawatan rutin: backup & update](#14-perawatan-rutin-backup--update)
15. [Masalah yang sering terjadi](#15-masalah-yang-sering-terjadi)

---

## 1. Masuk ke wp-admin & mengenal menu

1. Buka `https://blackrollblinds.com/wp-admin`
2. Masukkan **Username** dan **Password** → klik **Log In**.
3. Anda masuk ke **Dashboard**.

Menu di sidebar kiri yang akan sering dipakai:

| Menu | Fungsinya |
|---|---|
| **Posts** | Artikel blog (halaman `/artikel/`) |
| **Media** | Semua foto yang diunggah |
| **Pages** | Halaman statis (Tentang Kami, Kebijakan Privasi, dll.) |
| **Portfolio** | Proyek pemasangan (halaman `/portofolio/`) |
| **Shades** | Daftar warna / SKU kain (tampil di halaman `/material-warna/`) |
| **Blackroll** | Pengaturan nomor WhatsApp & link Google Maps |
| **Appearance → Editor** | Mengubah footer (kontak, sosial media) dan menu |
| **Languages** | Pengaturan bahasa (Indonesia / English) |
| **Rank Math** | Pengaturan SEO |
| **LiteSpeed Cache** | Membersihkan cache setelah perubahan |
| **Users** | Akun pengguna wp-admin |

> **Tips:** Selalu buka website di tab lain (sebaiknya jendela **Incognito/Private**) untuk
> mengecek hasil perubahan.

---

## 2. Aturan aman (wajib dibaca)

Agar tampilan website tetap rapi, ikuti aturan berikut:

**Halaman yang JANGAN diedit di editor halaman.** Isi halaman-halaman ini diambil otomatis
dari desain tema. Kalau dibuka dan disimpan ulang di editor, tampilannya bisa rusak.

- Beranda
- Produk, Blinds Manual, Blinds Motorized
- Material & Warna
- Kontak
- Terima Kasih, Pengiriman Gagal

Isi yang bisa diubah dari halaman-halaman tersebut (warna/SKU, nomor WhatsApp, proyek
portofolio) sudah punya menu sendiri — lihat bab-bab di bawah.

**Jangan pernah:**

- mengubah **Settings → Permalinks** (selain menekan *Save Changes* tanpa mengubah apa pun),
- menghapus halaman, tema **Blackroll**, atau plugin **Blackroll Core**,
- menonaktifkan plugin **Polylang**, **Rank Math**, **Solid Security**, atau **LiteSpeed Cache**,
- memasang plugin page builder (Elementor, Divi, dll.),
- mencentang **Settings → Reading → Discourage search engines** (website akan hilang dari Google).

**Lebih baik "Draft" daripada "Hapus".** Kalau ingin menyembunyikan warna, proyek, atau
artikel, ubah statusnya menjadi **Draft**. Data tetap tersimpan dan bisa ditampilkan lagi.

---

## 3. Mengunggah gambar (Media)

### Ukuran gambar yang disarankan

| Untuk | Bentuk | Ukuran minimal | Catatan |
|---|---|---|---|
| Swatch warna (kotak kecil) | Persegi 1:1 | 300 × 300 px | Close-up tekstur kain, tanpa tulisan |
| Preview warna (gambar besar) | Persegi 1:1 | 900 × 900 px | Foto blind terpasang / kain penuh |
| Foto proyek portofolio | Landscape 4:3 | 1600 × 1200 px | Foto ruangan hasil pemasangan |
| Gambar utama artikel | Landscape 4:3 | 1200 × 900 px | Tanpa teks besar di dalam foto |

- Format: **JPG** atau **WebP**. Hindari PNG untuk foto (ukurannya besar).
- Ukuran file: usahakan **di bawah 500 KB** per foto. Kompres dulu di
  [squoosh.app](https://squoosh.app) atau [tinypng.com](https://tinypng.com) kalau lebih besar.
- Nama file yang jelas membantu SEO, contoh: `roller-blind-blackout-ml002b.jpg`
  (bukan `IMG_20261005_1234.jpg`).

### Cara mengunggah

1. **Media → Add New Media File** → seret foto ke kotak upload (atau klik **Select Files**).
2. Setelah selesai, klik fotonya → isi **Alternative Text** (deskripsi singkat isi foto,
   contoh: `Roller blind blackout warna abu-abu di ruang tamu`). Ini penting untuk Google
   dan pengguna tunanetra.

Foto juga bisa langsung diunggah dari dalam form Shade/Proyek/Artikel lewat tombol
**Select image** atau **Set featured image** → tab **Upload files**.

---

## 4. Menambah & mengedit warna / SKU produk (Shades)

Setiap warna kain = satu entri **Shade**. Semua shade otomatis tampil di halaman
**Material & Warna** (`/material-warna/`), di bagian pemilih warna.

Cara kerja pemilih warna di website:
pengunjung memilih **Material** (Blackout / Solar Screen / Dimout / Zebra Blinds) →
**Seri Warna** (Black Series / White Series) → muncul kotak-kotak **Warna** yang cocok →
saat diklik, foto besar di sebelah kanan berganti.

### 4.1 Menambah warna baru

1. Klik **Shades → Add New** di sidebar kiri.
2. **Judul (Add title):** nama warna yang tampil ke pengunjung. Bisa nama warna
   (contoh `Charcoal`) atau kode SKU (contoh `ML008H`). Daftar warna di website diurutkan
   **A–Z berdasarkan judul**.
3. Gulir ke panel **Shade Details**, isi:

   | Kolom | Isi |
   |---|---|
   | **SKU Code** | Kode SKU persis seperti di daftar produk, huruf besar. Contoh `ML002B`. Ditampilkan sebagai "SKU …" di label preview. |
   | **Swatch Image (ID)** | Klik **Select image** → pilih/unggah foto close-up kain → **Use this image**. Angka ID terisi otomatis dan muncul thumbnail kecil. |
   | **Preview Image (ID)** | Klik **Select image** → foto besar yang tampil di sebelah kanan pemilih warna. |
   | **Preview — Blackout (ID)** | *Opsional.* Foto khusus yang tampil saat material **Blackout** dipilih. Kosongkan kalau sama dengan Preview Image. |
   | **Preview — Solar Screen (ID)** | *Opsional.* Foto khusus saat material **Solar Screen** dipilih. |
   | **Available in Blackout** | Centang jika material warna ini Blackout. |
   | **Available in Solar Screen** | Centang jika material warna ini Solar Screen. |
   | **Material Type (catalogue)** | **WAJIB.** Ketik **salah satu** kode di bawah ini, huruf kecil, persis sama. |

   **Kode Material Type:**

   | Ketik ini | Tampil sebagai |
   |---|---|
   | `blackout` | Blackout |
   | `solar_screen` | Solar Screen |
   | `dimout` | Dimout |
   | `zebra` | Zebra Blinds |

   > ⚠️ Kalau kolom ini kosong atau salah ketik (misalnya `Blackout`, `solar screen`,
   > `solar-screen`), warna **tidak akan muncul** di website.
   >
   > Satu shade hanya untuk **satu** material. Jika warna yang sama tersedia di dua
   > material, buat **dua shade** terpisah (contoh judul: `Grey — Blackout` dan
   > `Grey — Solar Screen`).

4. Di panel kanan, bagian **Color Series**: centang **Black Series** atau **White Series**
   (pilih satu saja). **Wajib** — tanpa seri, warna tidak akan muncul.
5. *(Opsional)* **Set featured image**: dipakai sebagai cadangan kalau Swatch/Preview kosong.
6. Jika ada kotak **Languages** di panel kanan, pastikan **Bahasa Indonesia**.
7. Klik **Publish**.
8. Bersihkan cache (bab 11), lalu cek di `/material-warna/`: pilih material dan seri yang
   sesuai → warna baru muncul.

> **Kalau belum punya foto:** shade tetap bisa dipublish. Kotak warnanya akan menampilkan
> **kode SKU** sebagai teks, dan area preview menampilkan pesan "Foto warna ini segera hadir".
> Lengkapi fotonya kapan saja.

### 4.2 Mengedit warna / SKU / foto

1. **Shades** → arahkan kursor ke warna yang ingin diubah → klik **Edit**.
   (Gunakan kotak **Search Shades** di kanan atas untuk mencari berdasarkan judul.)
2. Ubah kolom yang diperlukan:
   - **Ganti foto:** klik **Select image** pada kolom yang ingin diganti → pilih foto baru.
   - **Hapus foto:** kosongkan angka di kolom ID (atau ketik `0`).
   - **Ganti SKU:** ubah teks di **SKU Code**. Jika judul juga berupa SKU, ganti judulnya juga.
3. Klik **Update** → bersihkan cache (bab 11).

### 4.3 Menyembunyikan / menghapus warna

- **Sembunyikan sementara** (misalnya stok habis): buka shade → di panel kanan ubah
  status ke **Draft** → **Update**. Bisa di-**Publish** lagi kapan saja.
- **Hapus permanen:** **Shades** → arahkan kursor → **Trash**. Masih bisa dipulihkan dari
  tab **Trash** selama 30 hari.

### 4.4 Catatan tentang foto produk lain

Foto-foto di halaman **Beranda**, **Blinds Manual**, dan **Blinds Motorized**
(galeri produk, foto ruangan, model 3D) adalah bagian dari desain tema dan tidak diubah
lewat menu Shades. Untuk mengganti foto-foto tersebut, kirim foto barunya ke tim MEA.

---

## 5. Mengelola Seri Warna

Seri warna adalah tombol **Black Series / White Series** di halaman Material & Warna.

- **Melihat / mengganti nama seri:** **Shades → Color Series** → arahkan kursor ke seri →
  **Quick Edit** → ubah **Name** → klik tombol **Update** biru. Jangan ubah kolom **Slug**.
- **Menambah seri baru** (misalnya *Premium Series*): di halaman yang sama isi **Name** →
  klik tombol **Add New** di bawah form. Tombol seri baru otomatis muncul di website setelah
  minimal satu shade dimasukkan ke seri itu.
- Seri yang tidak punya shade tidak akan tampil di website.

---

## 6. Mengganti nomor WhatsApp, Instagram, TikTok & alamat

Kontak tampil di beberapa tempat. Saat nomor atau akun berganti, ikuti **semua** langkah
di bawah agar tidak ada nomor lama yang tertinggal.

### 6.1 Nomor WhatsApp utama (menu Blackroll)

1. Klik menu **Blackroll** di sidebar kiri.
2. Kolom **Primary WhatsApp (D4):** ketik nomor baru dengan format internasional, contoh
   `+6281338388500` (boleh juga `0813-3838-8500`).
3. Klik **Save Changes**.

Nomor ini otomatis dipakai di:

- tombol **Konsultasi Gratis** di bagian atas Beranda,
- daftar kontak **WhatsApp** di halaman Kontak,
- tombol WhatsApp cadangan pada form Kontak.

Kolom lain di halaman yang sama:

- **Google Maps URL (Contact):** link Google Maps toko (buka toko di Google Maps → **Share**
  → **Copy link**). Dipakai untuk data lokasi bisnis yang dibaca Google.
- **Secondary WhatsApp** dan **GA4 Measurement ID**: biarkan seperti apa adanya.

Bagian **Locked configuration** di bawahnya hanya informasi — tidak perlu diubah.

### 6.2 Footer: WhatsApp, alamat, Instagram & TikTok

Footer (bagian hitam paling bawah di semua halaman) diubah lewat **Site Editor**:

1. **Appearance → Editor**.
2. Klik **Patterns** → di bagian **Template Parts** pilih **Footer** → klik area footer
   untuk mulai mengedit.
3. **Mengganti nomor WhatsApp di footer:**
   1. Klik teks nomor WhatsApp (`0813-3838-8500`), ketik nomor baru.
   2. Blok teks nomor tersebut → klik ikon **Link** (🔗) di toolbar → ganti alamat link
      menjadi `https://wa.me/62xxxxxxxxxx` (nomor tanpa `+`, tanpa `0` di depan, tanpa
      spasi/strip; contoh `https://wa.me/6281338388500`) → tekan **Enter**.
4. **Mengganti alamat:** klik teks alamat, ketik alamat baru.
5. **Mengganti link Instagram / TikTok:**
   1. Di bagian **Ikuti Kami**, klik ikon **Instagram**.
   2. Muncul kotak link → ganti dengan link profil baru, contoh
      `https://instagram.com/blackroll.official` → tekan **Enter**.
   3. Ulangi untuk ikon **TikTok**, contoh `https://www.tiktok.com/@blackroll.blinds`.
6. **Menambah ikon sosial media lain** (misalnya Shopee, YouTube, Facebook): klik salah satu
   ikon → klik tombol **+** di sebelah ikon terakhir → cari nama platform → isi link-nya.
   *(Shopee tidak punya ikon bawaan — gunakan ikon **Link** dengan link toko Shopee.)*
7. Klik **Save** di kanan atas → **Save** lagi untuk konfirmasi.
8. Bersihkan cache (bab 11) dan cek footer di website.

> Kalau footer jadi berantakan setelah diedit: **Appearance → Editor → Patterns → Footer**
> → menu titik tiga (⋮) → **Reset** untuk kembali ke footer asli dari tema. Setelah itu
> ulangi perubahan dengan hati-hati.

### 6.3 Halaman Kebijakan Privasi

Halaman Kebijakan Privasi menuliskan nomor WhatsApp di dalam teksnya. Kalau nomor berganti:
**Pages → Kebijakan Privasi → Edit** → cari nomor lama, ganti dengan yang baru (termasuk
link-nya, seperti langkah 6.2 nomor 3) → **Update**.

### 6.4 Halaman Kontak

Daftar Instagram, TikTok Shop, dan Shopee serta alamat toko di halaman **Kontak** adalah
bagian dari desain tema. Jika username akun atau alamat toko berganti, kabari tim MEA untuk
memperbaruinya. (Nomor WhatsApp di halaman ini sudah otomatis mengikuti bab 6.1.)

### Checklist ganti nomor WhatsApp

- [ ] Menu **Blackroll** → Primary WhatsApp → Save
- [ ] **Footer** di Site Editor → teks nomor + link `wa.me` → Save
- [ ] **Kebijakan Privasi** → teks nomor + link → Update
- [ ] **Purge cache** (bab 11)
- [ ] Cek di incognito: Beranda (tombol Konsultasi Gratis), halaman Kontak, footer

---

## 7. Menambah & mengedit artikel

Artikel tampil di halaman **Artikel** (`/artikel/`), 9 artikel per halaman, urut dari yang
terbaru.

### 7.1 Menulis artikel baru

1. **Posts → Add New Post**.
2. **Judul:** ketik di bagian *Add title*. Buat jelas dan mengandung kata kunci, contoh
   `5 Tips Memilih Roller Blind untuk Kamar Tidur`.
3. **Isi artikel:** klik di bawah judul dan mulai menulis. Tekan **Enter** untuk paragraf baru.
   Untuk menambah elemen lain, klik tombol **+** (Block Inserter):
   - **Heading** — subjudul (gunakan **H2** untuk subjudul utama, **H3** untuk sub-bagian),
   - **Image** — foto di tengah artikel,
   - **List** — daftar poin,
   - **Quote**, **Table**, **Buttons** — sesuai kebutuhan.

   Untuk membuat link: blok teks → ikon **Link** (🔗) → tempel alamat → **Enter**.
4. Di panel kanan (tab **Post**), isi:
   - **Featured image** → **Set featured image** → pilih/unggah foto (gambar utama yang
     tampil di daftar artikel dan bagian atas artikel). **Wajib**, agar daftar artikel rapi.
   - **Categories** → centang salah satu: **Panduan**, **Inspirasi**, atau **Produk**.
   - **Excerpt** → 1–2 kalimat ringkasan yang tampil di daftar artikel.
   - **Slug / URL** → otomatis dari judul. Boleh dipendekkan, contoh
     `tips-roller-blind-kamar-tidur`. Hanya huruf kecil, angka, dan tanda `-`.
   - **Language** (jika ada) → **Bahasa Indonesia**.
5. **SEO (Rank Math):** klik ikon Rank Math di kanan atas editor:
   - **Focus Keyword:** kata kunci utama, contoh `roller blind kamar tidur`.
   - **Edit Snippet** → isi **Description** (±150 karakter) yang akan tampil di hasil Google.
   - Ikuti saran Rank Math sebisanya; skor tidak harus 100.
6. Klik **Publish** → **Publish** lagi untuk konfirmasi.
   - Ingin terbit nanti? Klik tanggal di panel kanan (**Publish: Immediately**) → pilih
     tanggal & jam → tombol berubah menjadi **Schedule**.
   - Belum selesai? Klik **Save draft**.
7. Bersihkan cache (bab 11) dan cek `/artikel/`.

### 7.2 Mengedit / menghapus artikel

- **Edit:** **Posts** → arahkan kursor ke judul → **Edit** → ubah → **Update**.
- **Sembunyikan:** ubah status ke **Draft**.
- **Hapus:** **Posts** → **Trash** (bisa dipulihkan dari tab **Trash** selama 30 hari).

### 7.3 Mengelola kategori

**Posts → Categories**: tambah kategori baru (isi **Name** → **Add New Category**) atau ubah
nama kategori lewat **Quick Edit**. Jangan menghapus kategori yang masih dipakai artikel.

### Tips menulis artikel

- Panjang ideal 600–1.200 kata, dengan subjudul setiap 2–4 paragraf.
- Satu artikel fokus menjawab satu pertanyaan calon pembeli (contoh: "Blackout vs Solar
  Screen, pilih yang mana?").
- Akhiri dengan ajakan konsultasi, lalu tautkan ke halaman `/kontak/`.
- Jangan menyalin artikel dari website lain.

---

## 8. Menambah proyek Portofolio

Proyek tampil di halaman **Portofolio** (`/portofolio/`) dan bisa difilter per tipe.
Proyek yang ditandai **Featured** juga tampil di bagian "Portofolio Pilihan" di Beranda.

### 8.1 Menambah proyek

1. **Portfolio → Add New** di sidebar kiri.
2. **Judul:** nama proyek, contoh `Rumah Tinggal — Dago, Bandung`.
3. **Isi (opsional):** cerita singkat proyek — kebutuhan klien, produk yang dipakai, hasilnya.
   Tampil di halaman detail proyek. Bisa juga menambahkan blok **Gallery** berisi foto-foto
   tambahan proyek.
4. Panel kanan → **Featured image** → unggah **1 foto terbaik** (landscape). Foto inilah
   yang tampil di grid Portofolio dan Beranda.
5. Panel kanan → **Project Types** → centang **Residensial**, **Kantor**, atau **Apartemen**.
   Ini yang dipakai tombol filter di halaman Portofolio.
6. Panel **Project Details** di bawah editor:
   - **Featured (Homepage highlight):** centang jika proyek ini ingin tampil di Beranda.
     Beranda menampilkan **3 proyek Featured terbaru**.
   - Kolom **Caption, Product Used, Location, Gallery Image IDs** boleh diisi sebagai
     catatan arsip proyek.
7. **Language** (jika ada) → **Bahasa Indonesia**.
8. **Publish** → bersihkan cache (bab 11).

### 8.2 Mengganti proyek di Beranda

Beranda selalu menampilkan 3 proyek **Featured** yang paling baru. Untuk mengganti:
hilangkan centang **Featured** di proyek lama → **Update**, lalu centang di proyek baru.

### 8.3 Menambah tipe proyek baru

**Portfolio → Project Types** → isi **Name** (contoh `Hotel`) → klik tombol **Add New** di bawah form.
Tombol filter baru otomatis muncul setelah ada minimal satu proyek bertipe tersebut.

---

## 9. Mengedit halaman Tentang Kami & Kebijakan Privasi

Dua halaman ini bebas diedit:

1. **Pages** → arahkan kursor ke **Tentang Kami** (atau **Kebijakan Privasi**) → **Edit**.
2. Klik teks yang ingin diubah, langsung ketik. Ganti foto: klik foto → **Replace** di toolbar.
3. Klik **Save** / **Update** → bersihkan cache.

Untuk Kebijakan Privasi, perbarui juga tanggal di baris **"Terakhir diperbarui"** setiap
kali isinya diubah.

> Ingat: halaman di daftar **bab 2** (Beranda, Produk, Material & Warna, Kontak, dll.)
> **tidak** diedit dari menu Pages.

---

## 10. Mengubah menu navigasi

Menu di bagian atas (header) dan daftar **Peta Situs** di footer diubah lewat Site Editor:

1. **Appearance → Editor → Patterns → Template Parts → Header** (atau **Footer** untuk
   Peta Situs).
2. Klik menu navigasi → klik salah satu item menu:
   - **Ganti teks menu:** klik item → ketik teks baru.
   - **Ganti link:** klik item → ikon **Link** → ganti alamat.
   - **Tambah item:** klik tombol **+** di ujung menu → cari halaman → klik.
   - **Hapus item:** klik item → menu titik tiga (⋮) → **Delete**.
   - **Ubah urutan:** klik item → panah **←/→** (atau ↑/↓) di toolbar.
3. **Save** → bersihkan cache.

> Kalau menu jadi berantakan: menu titik tiga (⋮) pada Header/Footer → **Reset** untuk
> kembali ke versi asli tema.

---

## 11. Membersihkan cache

Website memakai cache agar cepat dibuka. Akibatnya, **perubahan kadang belum terlihat**
sampai cache dibersihkan.

**Lakukan setelah setiap perubahan:**

1. Di bar hitam paling atas wp-admin, arahkan kursor ke ikon/menu **LiteSpeed Cache**.
2. Klik **Purge All**.
3. Buka website di jendela **Incognito/Private** untuk mengecek.

Kalau masih belum berubah, tunggu 1–2 menit lalu refresh dengan **Ctrl + F5**
(Mac: **Cmd + Shift + R**).

---

## 12. Mengelola pengguna & password

### Menambah akun untuk staf

1. **Users → Add New User**.
2. Isi **Username**, **Email**, nama → klik **Generate password** (atau tentukan sendiri,
   minimal 12 karakter campuran huruf, angka, simbol).
3. **Role** — pilih sesuai kebutuhan:

   | Role | Bisa apa |
   |---|---|
   | **Author** | Menulis & menerbitkan artikel sendiri saja |
   | **Editor** | Mengelola semua artikel, halaman, warna (Shades), dan portofolio |
   | **Administrator** | Semua akses termasuk pengaturan, plugin, dan pengguna — **berikan hanya ke 1–2 orang** |

4. **Add New User**.

### Mengganti password

**Users → Profile** → gulir ke **Account Management** → **Set New Password** → **Update Profile**.

### Menghapus akses staf yang keluar

**Users** → arahkan kursor → **Delete** → pilih **Attribute all content to:** akun lain agar
artikelnya tidak ikut terhapus → **Confirm Deletion**.

> Jangan pernah berbagi satu akun untuk banyak orang. Buat akun masing-masing.

---

## 13. Data dari form Kontak

Form konsultasi di halaman **Kontak** terhubung ke **Sebari**. Data calon pelanggan
(nama, nomor WhatsApp, email) yang mengisi form masuk ke **dashboard Sebari**, bukan ke
wp-admin. Setelah mengirim form, pengunjung diarahkan ke halaman **Terima Kasih**.

Cek dan tindak lanjuti leads secara rutin dari akun Sebari Blackroll.

---

## 14. Perawatan rutin: backup & update

| Kapan | Apa | Di mana |
|---|---|---|
| Sebelum perubahan besar | Buat backup | hPanel Hostinger → **Websites → Backups** |
| Setiap minggu | Cek leads masuk | Dashboard Sebari |
| Setiap bulan | Update plugin | wp-admin → **Plugins** / **Dashboard → Updates** |
| Setiap bulan | Cek semua halaman utama tampil normal | Website (incognito) |

**Cara update plugin dengan aman:**

1. Pastikan ada backup terbaru (hPanel → Backups).
2. **Dashboard → Updates** → centang plugin yang ada pembaruan → **Update Plugins**.
   Update satu per satu lebih aman.
3. Bersihkan cache → cek Beranda, Material & Warna, Kontak, dan Artikel di incognito.
4. Kalau ada yang rusak setelah update: restore backup dari hPanel
   (**Websites → Backups → Restore**) dan hubungi tim MEA.

Tema **Blackroll** dan plugin **Blackroll Core** dibuat khusus untuk website ini dan
tidak muncul di daftar update WordPress. Pembaruannya dilakukan oleh tim MEA.

---

## 15. Masalah yang sering terjadi

| Masalah | Penyebab & solusi |
|---|---|
| Perubahan tidak muncul di website | Cache. Lakukan **Purge All** (bab 11), cek di incognito. |
| Warna baru tidak muncul di Material & Warna | Cek 3 hal: status **Published**; **Material Type** diisi persis `blackout` / `solar_screen` / `dimout` / `zebra`; **Color Series** sudah dicentang. Lalu purge cache. |
| Kotak warna berisi tulisan kode SKU, bukan foto | **Swatch Image** belum diisi. Edit shade → **Select image** pada Swatch Image. |
| Foto preview warna tidak muncul | **Preview Image** belum diisi, atau foto terhapus dari Media. Pilih ulang fotonya. |
| Proyek tidak muncul di Beranda | Centang **Featured**, dan pastikan termasuk 3 proyek Featured terbaru. |
| Proyek tidak ikut terfilter | **Project Types** belum dipilih. |
| Artikel tampil tanpa gambar di daftar artikel | **Featured image** belum diisi. |
| Nomor WhatsApp lama masih muncul | Ikuti checklist di bab 6 (ada 3 tempat), lalu purge cache. |
| Footer/menu berantakan setelah diedit | Site Editor → Footer/Header → ⋮ → **Reset**. |
| Gagal upload foto ("exceeds maximum upload size") | Foto terlalu besar. Kompres dulu (bab 3). |
| Lupa password | Di halaman login klik **Lost your password?** → cek email. |
| Website putih / error | Jangan ubah apa pun. Screenshot pesan errornya dan hubungi tim MEA. |

---

*Panduan ini disusun oleh tim MEA Digital Marketing untuk serah terima website Blackroll.
Jika ada pertanyaan atau kebutuhan perubahan desain, hubungi tim MEA.*
