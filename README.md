# DEL Lab Equipment & Asset Tracking System (DEL-LEATS)
### Ujian Tengah Semester (UTS) Praktikum - Pemrograman dan Pengujian Aplikasi Web (12S3101)
**Institut Teknologi Del - Semester Ganjil T.A. 2026/2027**

---

## Identitas Mahasiswa
- **Nama Mahasiswa** : Fredrick Laurensius Aritonang
- **NIM** : 12S24001
- **Program Studi** : S1 Sistem Informasi
- **Dosen Pengampu** : Chandro Pardede, S.Kom., M.Sc.
- **Waktu Ujian** : Kamis, 29 Oktober 2026

---

## 1. Deskripsi Proyek
**DEL-LEATS (DEL Lab Equipment & Asset Tracking System)** adalah modul inti aplikasi web terpadu berbasis arsitektur **Model-View-Controller (MVC)** yang dirancang untuk mengatasi permasalahan inventarisasi dan peminjaman perangkat laboratorium di Institut Teknologi Del. 

Sistem ini memecahkan berbagai kendala operasional:
1. **Pencegahan Double-Booking**: Memantau status unit secara real-time (`tersedia`, `dipinjam`, `dalam_perbaikan`) dan mencegah pengajuan peminjaman untuk perangkat yang tidak siap pakai.
2. **Dokumentasi Visual & Spesifikasi**: Menyimpan foto fisik aset dengan nama berkas unik serta rincian spesifikasi teknis dan kondisi fisik perangkat (`baik`, `rusak_ringan`, `rusak_berat`).
3. **Katalog Aset Berpaginasi & Multi-Filter**: Menyajikan katalog responsif (3 kolom desktop, 2 kolom tablet, 1 kolom smartphone) dengan fitur pencarian parsial (kata kunci), filter kategori, dan status ketersediaan.
4. **Validasi Aturan Bisnis Ketat & Kompensasi Sewa**: Menegakkan aturan batas peminjaman maksimal 7 hari kalender, validasi identitas (NIM 8 digit Del, surel institusi, tanggal peminjaman tidak lampau), serta kalkulasi otomatis kompensasi biaya sewa harian.

---

## 2. Struktur Skema Basis Data & Relasi
Aplikasi memiliki 3 tabel utama yang saling berelasi:

```
+------------------+          +-------------------+          +---------------------+
|    categories    | 1      * |     equipments    | 1      * |    loan_requests    |
+------------------+----------+-------------------+----------+---------------------+
| id (PK)          |          | id (PK)           |          | id (PK)             |
| name (VARCHAR)   |          | category_id (FK)  |          | equipment_id (FK)   |
| code (VARCHAR)   |          | asset_code (UNIQ) |          | borrower_name       |
| created_at       |          | name              |          | borrower_nim        |
| updated_at       |          | specification     |          | borrower_email      |
+------------------+          | condition (ENUM)  |          | loan_date           |
                              | status (ENUM)     |          | expected_return_date|
                              | daily_rental_fee  |          | purpose             |
                              | image_path        |          | status (ENUM)       |
                              | created_at        |          | total_estimated_fee |
                              | updated_at        |          | created_at          |
                              +-------------------+          | updated_at          |
                                                             +---------------------+
```

### Relasi Eloquent:
- `Category hasMany Equipment` / `Equipment belongsTo Category` (On-Delete Restrict)
- `Equipment hasMany LoanRequest` / `LoanRequest belongsTo Equipment` (On-Delete Cascade)

---

## 3. Arsitektur RESTful Routing & Endpoint

| Metode HTTP | Endpoint URI | Controller & Action | Fungsi & Deskripsi |
|---|---|---|---|
| `GET` | `/` | Redirect to `/equipments` | Mengarahkan halaman utama ke katalog aset |
| `GET` | `/equipments` | `EquipmentController@index` | Katalog berpaginasi (6 aset/halaman), pencarian keyword & multi-filter |
| `GET` | `/equipments/create` | `EquipmentController@create` | Tampilan formulir penambahan aset inventaris baru |
| `POST` | `/equipments` | `EquipmentController@store` | Validasi server-side, upload foto fisik, & penyimpanan aset |
| `GET` | `/equipments/{id}` | `EquipmentController@show` | Detail spesifikasi, foto fisik, status, & riwayat peminjaman aset |
| `GET` | `/loans` | `LoanController@index` | Tampilan riwayat seluruh transaksi peminjaman laboratorium |
| `GET` | `/loans/create` | `LoanController@create` | Formulir pengajuan peminjaman aset (mendukung query `equipment_id`) |
| `POST` | `/loans` | `LoanController@store` | Validasi aturan bisnis, kalkulasi biaya, & mutasi status unit ke `dipinjam` |

---

## 4. Fitur Utama & Kesesuaian Spesifikasi Soal

### A. Desain Antarmuka Semantik HTML5 & CSS Responsif
- Master layout Blade (`resources/views/layouts/app.blade.php`) memanfaatkan elemen semantik: `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, dan `<footer>`.
- Grid responsif:
  - **Desktop** (> 1024px): 3 Kolom
  - **Tablet** (641px - 1024px): 2 Kolom
  - **Ponsel Cerdas** (<= 640px): 1 Kolom
- **Indikator Visual Status Badge Kontras**:
  - `Tersedia`: Latar `#DCFCE7`, Teks `#166534`, Border `#86EFAC`
  - `Dipinjam`: Latar `#FEF9C3`, Teks `#854D0E`, Border `#FDE047`
  - `Dalam Perbaikan`: Latar `#FEE2E2`, Teks `#991B1B`, Border `#FCA5A5`
- Navigasi bar terintegrasi dengan penanda aktif (*active link state*) untuk: **Katalog Aset**, **Tambah Aset**, dan **Riwayat Peminjaman**.

### B. Pengelolaan Formulir, Validasi Server-Side, & Upload Berkas
- Proteksi serangan CSRF menggunakan direktif `@csrf` pada seluruh formulir POST.
- Enctype formulir: `enctype="multipart/form-data"`.
- Upload berkas foto fisik disimpan pada direktori publik `storage/app/public/equipments` dengan penamaan unik berbasis timestamp dan kode aset slug (`time() . '_' . Str::slug($asset_code) . '.' . $extension`) untuk mencegah penimpaan berkas (*file overwriting*).
- Retensi input formulir saat terjadi error menggunakan helper `old('field')`.
- Pesan galat spesifik muncul tepat di bawah field yang bermasalah.
- Pola *Post-Redirect-Get* (PRG) disertai notifikasi flash session (`[SUKSES]` dan `[GAGAL]`).

### C. Validasi Aturan Bisnis Peminjaman
- Penolakan bisnis jika unit berstatus selain `tersedia` (misal: `dipinjam` atau `dalam_perbaikan`).
- Format NIM tepat 8 karakter institusi Del (misal: `12S24001`).
- Format surel valid (`filter_var` / `email`).
- Tanggal peminjaman tidak boleh tanggal lampau (`after_or_equal:today`).
- Rencana pengembalian tidak boleh sebelum tanggal pinjam dan durasi sewa maksimal 7 hari kalender.
- Mutasi status otomatis unit menjadi `dipinjam` setelah permohonan berhasil diproses.

---

## 5. Panduan Menjalankan Sistem

### Persyaratan Lingkungan
- PHP >= 8.2 (dengan ekstensi `pdo_sqlite`, `fileinfo`, `mbstring`, `openssl`, `curl`)
- Composer >= 2.x

### Langkah Instalasi
1. Buka terminal pada direktori proyek.
2. Salin berkas konfigurasi lingkungan jika belum ada:
   ```bash
   cp .env.example .env
   ```
3. Generate application key:
   ```bash
   php artisan key:generate
   ```
4. Buat tautan simbolik storage publik:
   ```bash
   php artisan storage:link
   ```
5. Eksekusi migrasi dan database seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```
6. Jalankan web server lokal Laravel:
   ```bash
   php artisan serve
   ```
   Akses aplikasi pada peramban melalui alamat: `http://localhost:8000` (atau `http://127.0.0.1:8000`).

---

## 6. Verifikasi Kasus Uji Otomatis (Automated Testing)
Seluruh pengujian unit dan fungsional (HOTS Test Cases 1, 2, dan 3) dapat dijalankan dengan perintah:
```bash
php artisan test
```

### Ringkasan Hasil Pengujian:
- `test_kasus_uji_1_pendaftaran_inventaris_baru_yang_valid`: **PASS** (HTTP 302, Flash Success Notification, Database record ID 7, Image path terdaftar, Katalog render unit baru).
- `test_kasus_uji_2_pengajuan_peminjaman_data_tidak_valid`: **PASS** (HTTP 302 redirect back, Flash Error Notification, 6 pesan galat bisnis spesifik di bawah field, Retensi input old()).
- `test_kasus_uji_3_pencarian_dan_pemfilteran_katalog`: **PASS** (HTTP 200, Keterangan Ringkasan 2 dari 2 hasil untuk kata kunci 'drone', Render kartu EQ-DRN-001 & EQ-DRN-003, Tautan paginasi mempertahankan query string `search=drone&category_id=3&status=tersedia&page=1`).
- `test_positive_loan_request_and_status_mutation`: **PASS** (Kalkulasi total biaya akurat & mutasi status perangkat ke `dipinjam`).

---

## 7. Prosedur Pengumpulan Berkas Archive
Untuk membuat berkas ZIP pengumpulan sesuai format soal (`UTS_PRAKTIKUM_PPW_12S24001_Fredrick Laurensius Aritonang.zip` tanpa menyertakan `vendor/` dan `node_modules/`):
```powershell
Compress-Archive -Path .editorconfig, .env.example, .gitattributes, .gitignore, README.md, app, artisan, bootstrap, composer.json, composer.lock, config, database, package.json, phpunit.xml, public, resources, routes, storage, tests, vite.config.js -DestinationPath "UTS_PRAKTIKUM_PPW_12S24001_Fredrick Laurensius Aritonang.zip" -Force
```
