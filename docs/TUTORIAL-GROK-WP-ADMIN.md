# Tutorial untuk Grok Bot: Menyelesaikan Deploy Blackroll lewat wp-admin

Dokumen ini ditulis untuk **agen AI yang mengoperasikan browser** (Grok bot) dengan akses
**hanya ke wp-admin** blackrollblinds.com, tanpa SSH, tanpa Git, tanpa hosting panel.
Semua kode sudah selesai di repo (`main`). Tugas bot: memasang versi terbaru dan
menyelesaikan pengaturan yang hanya bisa dilakukan dari wp-admin.

Konteks lengkap revisi ada di `docs/PLAN-REVISI-PREMIUM.md`.

---

## 0. Aturan kerja (baca dulu)

1. **Kerjakan berurutan.** Setiap langkah punya bagian "Cek berhasil". Jangan lanjut ke
   langkah berikutnya sebelum cek itu lolos.
2. **Berhenti dan lapor ke Yohan** kalau:
   - layar yang muncul berbeda dari yang dijelaskan,
   - ada pesan error,
   - langkah meminta menghapus sesuatu yang tidak disebut di dokumen ini.
3. **Jangan pernah:**
   - menghapus halaman, post, media, tema, atau plugin (kecuali yang disebut eksplisit),
   - menginstal page builder atau plugin baru,
   - mengubah struktur Permalink,
   - mengedit Header/Footer/Template lewat Site Editor (selain tombol **Reset** di Langkah 5),
   - mengubah isi halaman Beranda, Manual, Motorized, Material & Warna, atau Kontak di editor
     (isinya diambil otomatis dari tema).
4. **Jangan menulis password atau kredensial** di laporan, chat, atau dokumen.
5. Setelah setiap langkah, **ambil screenshot** sebagai bukti untuk laporan akhir.

## Yang perlu disiapkan Yohan sebelum bot mulai

- [ ] Akun wp-admin (role **Administrator**) untuk bot.
- [ ] **Backup terbaru situs** dari hPanel Hostinger (Websites → Backups). Bot tidak bisa
      membuat backup sendiri; jangan mulai tanpa konfirmasi backup ada.
- [ ] Dua file ZIP dari repo GitHub `MEAgrup/Blackroll`, folder `release/`:
  - `blackroll-theme.zip`
  - `blackroll-core-plugin.zip`

  Cara download: buka file di GitHub → tombol **Download raw file**. Berikan kedua file ke bot.
  (Kalau file belum ada atau sudah usang, developer menjalankan `npm run package` lalu commit.)

---

## Langkah 1: Catat kondisi awal

**Tujuan:** punya pembanding sebelum/sesudah.

1. Buka `https://blackrollblinds.com/` di tab baru (tidak login). Screenshot bagian atas halaman.
2. Di wp-admin buka **Appearance → Themes**. Catat versi tema "Blackroll" (klik Theme Details).
3. Buka **Plugins**. Catat versi "Blackroll Core".

**Cek berhasil:** tiga screenshot tersimpan. Versi lama seharusnya `0.1.0`.

## Langkah 2: Update plugin Blackroll Core

**Tujuan:** memasang plugin versi `0.2.0`. Plugin dulu, baru tema, karena tema memakai fungsi
dari plugin.

1. **Plugins → Add New Plugin → Upload Plugin**.
2. Pilih `blackroll-core-plugin.zip` → **Install Now**.
3. WordPress akan bilang plugin sudah ada. Klik **Replace current with uploaded**.
4. Kalau plugin tidak aktif setelahnya, klik **Activate Plugin**.

**Cek berhasil:** di **Plugins**, "Blackroll Core" aktif dengan versi **0.2.0**.

**Kalau gagal** (misalnya "The link you followed has expired" / ukuran upload terlalu besar):
berhenti dan lapor ke Yohan. Batas upload perlu dinaikkan dari hPanel.

## Langkah 3: Update tema Blackroll

1. **Appearance → Themes → Add New Theme → Upload Theme**.
2. Pilih `blackroll-theme.zip` (±10 MB) → **Install Now**.
3. Klik **Replace active with uploaded**.

**Cek berhasil:** **Appearance → Themes → Blackroll → Theme Details** menunjukkan versi **0.2.0**.

## Langkah 4: Pengaturan umum

1. **Settings → General**:
   - **Site Title:** `Blackroll`
   - **Tagline:** `Black is Cool — Roller Blinds Premium`
   - **Timezone:** `Jakarta`
   - Klik **Save Changes**.
2. **Settings → Reading**:
   - "Your homepage displays" = **A static page**
   - Homepage = **Beranda**, Posts page = **Artikel**
   - **Search engine visibility:** pastikan **TIDAK** dicentang.
   - **Save Changes**.
3. **Settings → Permalinks**: jangan ubah apa pun, cukup klik **Save Changes** sekali.
   (Ini menyegarkan aturan URL setelah update plugin.)
4. **Blackroll** (menu di sidebar kiri):
   - **Primary WhatsApp (D4):** `+6281338388500`
   - **Save Changes**.

**Cek berhasil:** judul tab browser di homepage sekarang berisi "Blackroll", bukan
"blackrollblinds.com".

## Langkah 5: Reset Header dan Footer di Site Editor

**Tujuan:** memastikan logo baru dan footer baru dari tema yang tampil. Kalau template part
pernah diedit di Site Editor, WordPress terus memakai salinan lama dari database.

1. **Appearance → Editor → Patterns**.
2. Di bagian **Template Parts**, buka **Header**.
3. Kalau ada menu titik tiga (⋮) dengan pilihan **Reset** atau **Clear customizations**,
   klik dan konfirmasi. Kalau pilihan itu tidak ada, berarti belum pernah diedit; lewati.
4. Ulangi untuk **Footer**.
5. **Appearance → Editor → Templates**: periksa **Front Page**, **Pages**, dan
   **Product Page**. Kalau salah satunya bisa di-**Reset**, reset juga.

**Cek berhasil:** di homepage, header memakai **logo Blackroll** (ikon blind + tulisan
BLACKROLL™). Footer memakai logo dengan tagline **BLACK IS COOL**, dan Instagram mengarah ke
`instagram.com/blackroll.official`.

## Langkah 6: Polylang — jadikan Bahasa Indonesia bahasa utama

**Masalah yang diperbaiki:** di production Polylang hanya punya bahasa English, jadi Google
membaca seluruh situs sebagai English (`<html lang="en-US">`).

1. **Languages → Languages**.
2. Kalau **Bahasa Indonesia** belum ada di daftar: di form "Add new language" pilih
   **Bahasa Indonesia - id_ID**, lalu klik **Add new language**.
3. Di daftar bahasa, klik **ikon bintang** di baris Bahasa Indonesia untuk menjadikannya
   **default language**.
4. Kalau muncul notifikasi "There are posts, pages, categories or tags without language",
   klik tautan **"You can set them all to the default language"**.
5. Buka **Pages**. Lihat kolom bendera bahasa. Untuk setiap halaman berbahasa Indonesia yang
   masih bertanda English (Beranda, Tentang Kami, Produk, Blinds Manual, Blinds Motorized,
   Material & Warna, Portofolio, Artikel, Kontak, Terima Kasih, Pengiriman Gagal,
   Kebijakan Privasi):
   - centang halamannya → **Bulk actions → Edit → Apply**,
   - ubah **Language** menjadi **Bahasa Indonesia** → **Update**.
6. Ulangi untuk **Posts** (4 artikel), **Shades**, dan **Projects** jika kolom bahasa ada.
7. **Languages → Settings → URL modifications**: biarkan default bahasa **tidak** memakai
   awalan di URL (URL Indonesia tetap `/kontak/`, bukan `/id/kontak/`). Jangan ubah opsi
   lain. Kalau ragu, berhenti dan lapor.
8. **Settings → Permalinks → Save Changes** sekali lagi.

**Cek berhasil:**
- Buka homepage → klik kanan → **View Page Source** → baris pertama berisi
  `<html lang="id-ID"` (bukan `en-US`).
- `https://blackrollblinds.com/kontak/` terbuka normal.
- Tombol mengambang di pojok bawah bertuliskan **Hubungi Kami** (bukan "Contact Us") dan
  mengarah ke `/kontak/`.

## Langkah 7: Bersihkan cache

1. Di toolbar atas wp-admin cari menu **LiteSpeed Cache** → **Purge All**.
2. Kalau ada plugin cache lain atau menu Hostinger "Flush cache", jalankan juga.

**Cek berhasil:** buka homepage di jendela incognito; tampilan sudah versi baru (lihat
Langkah 8).

## Langkah 8: Verifikasi akhir (wajib, dengan screenshot)

Buka setiap URL di jendela incognito, desktop **dan** mode HP (DevTools → toggle device).

| URL | Yang harus terlihat |
|---|---|
| `/` | Hero hitam penuh layar, judul serif "Blinds Premium Terjangkau untuk *Hunian Modern*". Di kanan: **blind 3D** yang turun saat halaman terbuka dan tergulung saat di-scroll. Tombol warna (bulat) mengganti warna kain; tombol **Roller / Zebra** mengganti sistem. Di HP tanpa 3D: foto blind, dan tombol warna mengganti fotonya. |
| `/` (scroll ke bawah) | Strip krem: **100+**, **24**, **Ready**, **5**. Section "Roller Blind, *warna untuk setiap ruang*" dengan 6 foto. Kartu Zebra Blind. "Manual atau *Motorized*". Section hitam "Premium ada di *detail kecil*". |
| `/produk/blinds-manual/` | Galeri foto, lalu blind 3D dengan slider **"Geser untuk menggulung blind"**. |
| `/produk/blinds-motorized/` | Blind 3D **tanpa rantai**. |
| `/material-warna/` | Pilih material → setiap warna punya kotak foto (tidak ada kotak kosong). Preview di kanan menampilkan foto dan label. |
| `/kontak/` | Form Sebari tampil. |
| Footer (semua halaman) | Logo + "BLACK IS COOL", link Instagram `@blackroll.official`, Peta Situs berisi 8 link (tanpa "Terima Kasih"/"Pengiriman Gagal"). |

**Kalau ada yang tidak sesuai:** jangan coba memperbaiki dengan mengedit halaman.
Screenshot, catat URL dan yang terlihat, lapor ke Yohan.

## Langkah 9: Laporan ke Yohan

Kirim ringkasan:
- versi plugin & tema sebelum → sesudah,
- hasil setiap "Cek berhasil" (✅/❌),
- screenshot Langkah 8 (desktop + HP),
- hal yang dilewati atau gagal, dengan alasan.

---

## Tugas lanjutan (setelah klien mengirim daftar proyek)

Klien akan mengirim daftar proyek residence/commercial untuk Portofolio. Untuk setiap proyek:

1. **Projects → Add New**.
2. **Title:** nama proyek (contoh: `Rumah Tinggal — Dago, Bandung`).
3. **Featured image** (panel kanan): unggah 1 foto terbaik (landscape, minimal 1600 px).
4. Panel **Project Details**:
   - **Caption:** 1 kalimat (contoh: `Blackout charcoal untuk kamar tidur utama`).
   - **Product Used:** contoh `Roller Blind Blackout — Manual`.
   - **Location:** kota.
   - **Featured (Homepage highlight):** centang untuk maksimal **3** proyek terbaik.
   - **Gallery Image IDs:** unggah foto tambahan ke **Media**, lalu salin ID masing-masing
     (terlihat di URL saat foto dibuka: `post=123` → ID 123) dan tulis dipisah koma: `123,124,125`.
5. **Project Types** (panel kanan): pilih Residensial / Kantor / Apartemen.
6. Kalau Polylang aktif: set **Language** = Bahasa Indonesia.
7. **Publish**.
8. Untuk proyek contoh lama (Apartemen — Ruang Tamu, Kantor — Ruang Kerja, Rumah — Dapur,
   Home Office) yang fotonya poster: **jangan dihapus**. Hilangkan centang **Featured**,
   lalu ubah status ke **Draft** setelah minimal 3 proyek asli sudah publish.

**Cek berhasil:** section "Portofolio Pilihan" di homepage menampilkan 3 proyek asli; halaman
`/portofolio/` bisa difilter per tipe.

## Kalau ada yang rusak setelah update

1. **Jangan panik dan jangan hapus apa pun.**
2. Kalau halaman putih/error setelah Langkah 2–3: lapor ke Yohan dengan pesan errornya.
   Yohan bisa restore backup dari hPanel (Websites → Backups → Restore).
3. Kalau hanya tampilan yang aneh: jalankan Langkah 7 (purge cache) dulu, lalu cek lagi di
   incognito.
