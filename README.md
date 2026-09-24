# RealCommerce — Tugas Pertemuan 8: Sistem CRUD & Manajemen Produk

**Automated Testing & Quality:** ![PHPUnit Tests](https://img.shields.io/badge/PHPUnit-38%20passed-success?style=flat&logo=php) ![Playwright E2E](https://img.shields.io/badge/Playwright-E2E%20Passed-blue?style=flat&logo=playwright)

Aplikasi web sistem katalog belanja online dan manajemen inventaris produk (**CRUD**) modern berbasis **Plain PHP (MVC Pattern)** yang diintegrasikan dengan database **MySQL / MariaDB**, **Vite 8**, dan **Tailwind CSS v4**. Dibangun dengan pemisahan tanggung jawab yang rapi (*Model-View-Controller*, *Repository Pattern*, dan *Service Layer*), transaksi database ACID dengan pencatatan audit log (`storage/logs/database.log`), sistem migrasi & seeder mandiri (`composer db:migrate:fresh -- --seed`), validasi server-side yang ketat, ekspor laporan CSV asinkron (*Fetch API*), preview gambar produk reaktif, dan fitur reset database aman berbasis token kriptografi **UUID versi 7** (RFC 9562).

Website live: [realcommerce-crud.realitaa.dev](https://realcommerce-crud.realitaa.dev)  
Repository: [github.com/Realitaa/TugasWeb-Pertemuan8-CRUD](https://github.com/Realitaa/TugasWeb-Pertemuan8-CRUD)

---

## 📌 Pemenuhan Kriteria Tugas (Tugas Rutin 8 — CRUD Inventaris)

Berikut adalah matriks kesesuaian dan bukti implementasi lengkap terhadap seluruh kriteria wajib (**10/10 Requirements**) serta seluruh fitur bonus (**5/5 Bonus**):

### 📊 Matriks Kesesuaian Kriteria Wajib (10/10)

| No | Kriteria Wajib | Status | Bukti & Lokasi Implementasi dalam Kode |
|:--:|:---|:---:|:---|
| 1 | **Database `inventaris_db` dengan 3 tabel + FK** | ✅ **Terpenuhi** | [`.env.example`](.env.example), [`database/migrations/schema.sql`](database/migrations/schema.sql): Tabel `categories`, `suppliers`, dan `products` dilengkapi Foreign Key `fk_products_category` dan `fk_products_supplier` (`ON DELETE SET NULL ON UPDATE CASCADE`). |
| 2 | **Minimal 5 data seed per tabel** | ✅ **Terpenuhi** | [`database/Seeder/Seeder.php`](database/Seeder/Seeder.php): 8 kategori, 10 pemasok/supplier acak realistis via Faker (`id_ID`), dan 30+ produk lengkap dengan variasi harga, stok, dan relasi. |
| 3 | **Koneksi PDO dengan Singleton pattern** | ✅ **Terpenuhi** | [`database/Database.php`](database/Database.php) & [`database/pdo.php`](database/pdo.php): Class `Database\Database` dengan constructor privat, `__clone` privat, dan `getInstance(): PDO`. Teruji secara ketat pada unit test [`tests/DatabaseSingletonTest.php`](tests/DatabaseSingletonTest.php). |
| 4 | **Halaman list produk dengan JOIN 2 tabel** | ✅ **Terpenuhi** | [`app/Repositories/ProductRepository.php`](app/Repositories/ProductRepository.php) & [`resources/views/products/index.php`](resources/views/products/index.php): Query `LEFT JOIN categories` dan `LEFT JOIN suppliers` menampilkan nama kategori dan distributor pada tabel `/products`. |
| 5 | **Form create dengan dropdown kategori & supplier** | ✅ **Terpenuhi** | [`resources/views/products/create.php`](resources/views/products/create.php): Dropdown interaktif `<select name="category_id">` dan `<select name="supplier_id">` yang memuat data dinamis langsung dari database. |
| 6 | **Fitur update (form pre-filled) & delete (konfirmasi)** | ✅ **Terpenuhi** | [`resources/views/products/edit.php`](resources/views/products/edit.php): Formulir edit otomatis terisi (*pre-filled*) dengan data produk. [`resources/views/products/index.php`](resources/views/products/index.php): Tombol hapus memicu modal dialog konfirmasi modern di tengah layar dengan backdrop blur. |
| 7 | **SEMUA query input pakai prepared statements** | ✅ **Terpenuhi** | [`app/Repositories/`](app/Repositories/): Seluruh query SQL (`INSERT`, `UPDATE`, `DELETE`, `SELECT ... WHERE`) 100% menggunakan PDO `prepare()` dan binding parameter, bebas dari konkatenasi SQL mentah untuk mencegah SQL Injection. |
| 8 | **Output HTML pakai `htmlspecialchars()`** | ✅ **Terpenuhi** | [`app/Vite/helpers.php`](app/Vite/helpers.php): Fungsi global `e()` yang membungkus `htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8')` pada seluruh view untuk mencegah kerentanan XSS. Teruji pada [`tests/HelpersTest.php`](tests/HelpersTest.php). |
| 9 | **Flash message sukses/gagal (redirect pattern)** | ✅ **Terpenuhi** | [`app/Controllers/ProductController.php`](app/Controllers/ProductController.php): Menerapkan pola **Post/Redirect/Get (PRG)** dengan session flash (`flash('success', ...)` / `flash('error', ...)`), ditayangkan dengan toast notifikasi Toastify.js dan alert banner. |
| 10 | **UI rapi (boleh pakai CSS dari tugas sebelumnya)** | ✅ **Terpenuhi** | [`resources/css/app.css`](resources/css/app.css): Menggunakan **Tailwind CSS v4** modern, responsif desktop/mobile, palet warna harmonis, kartu preview gambar reaktif, icon library Iconify, dan modal dialog centered. |

---

### ⭐ Matriks Kesesuaian Kriteria Bonus (5/5)

| No | Fitur Bonus | Status | Bukti & Lokasi Implementasi dalam Kode |
|:--:|:---|:---:|:---|
| 1 | **Transaction pada delete (log aktivitas)** | ⭐ **Terpenuhi** | [`app/Services/ProductService.php`](app/Services/ProductService.php): Membungkus penghapusan dalam transaksi ACID (`beginTransaction()`, `commit()`, `rollBack()`) dan mencatat audit log ke [`storage/logs/database.log`](storage/logs/database.log). |
| 2 | **Fitur pencarian (search multi-kolom)** | ⭐ **Terpenuhi** | [`app/Repositories/ProductRepository.php`](app/Repositories/ProductRepository.php): Pencarian instan pada tabel inventaris (`?search=...`) mencakup nama produk, SKU, kategori, dan supplier. |
| 3 | **Fitur pagination interaktif** | ⭐ **Terpenuhi** | [`app/Repositories/ProductRepository.php`](app/Repositories/ProductRepository.php): Pagination server-side (`LIMIT :limit OFFSET :offset`) dengan navigasi nomor halaman, prev/next, dan indikator total baris data. |
| 4 | **Fitur export laporan (CSV)** | ⭐ **Terpenuhi** | [`app/Services/ProductService.php`](app/Services/ProductService.php) & [`resources/views/products/index.php`](resources/views/products/index.php): Tombol **Export Laporan** asinkron via **Fetch API** menggunakan **League CSV** (`league/csv`) tanpa reload layar, lengkap dengan spinner loading. |
| 5 | **Fitur reset database mandiri (UUID v7)** | ⭐ **Terpenuhi** | [`app/Services/DatabaseResetService.php`](app/Services/DatabaseResetService.php) & [`scripts/generate-key.js`](scripts/generate-key.js): Proteksi endpoint `POST /api/db/reset` dengan kunci kriptografi UUID v7 RFC 9562 untuk kemudahan pengujian demo secara aman. |

---

## 📸 Tangkapan Layar Aplikasi (Desktop Mode)

Berikut dokumentasi tampilan visual aplikasi web pada resolusi desktop yang dihasilkan secara otomatis menggunakan pengujian Playwright E2E:

### 1. Halaman Utama / Katalog Belanja Publik (`/`)
Menampilkan katalog produk dari database MySQL dengan pagination interaktif, harga rupiah, rating bintang, badge diskon, serta tombol pemesanan langsung terintegrasi dengan WhatsApp.
![Katalog Belanja Home](tests/screenshots/01-katalog-home.png)

### 2. Halaman Manajemen & Tabel Daftar Produk (`/products`)
Menampilkan daftar tabel inventaris lengkap dengan 2 JOIN relasi (Kategori & Supplier), status stok produk, pencarian multi-kolom, tombol ekspor laporan CSV asinkron, dan aksi edit/hapus.
![Tabel Manajemen Produk](tests/screenshots/02-manajemen-produk.png)

### 3. Halaman Form Tambah Produk Baru (`/products/create`)
Formulir input data produk baru lengkap dengan kolom wajib dan opsional (diskon, rating, deskripsi), dropdown kategori & supplier, serta kartu **Preview Gambar Reaktif** yang memuat gambar secara langsung saat URL dimasukkan.
![Form Tambah Produk Baru](tests/screenshots/03-tambah-produk.png)

### 4. Halaman Form Edit Produk (`/products/edit`)
Formulir pembaruan data produk yang sudah terisi (*pre-filled*) sesuai data di database, pengecekan keunikan SKU produk, dropdown kategori & supplier aktif, dan pembaruan thumbnail secara real-time.
![Form Edit Produk](tests/screenshots/04-edit-produk.png)

### 5. Modal Dialog Konfirmasi Hapus Produk
Modal dialog konfirmasi hapus berposisi di tengah layar secara horizontal dan vertikal dengan latar belakang redup (*backdrop blur*), memicu transaksi database ACID dan pencatatan audit log saat disetujui.
![Modal Konfirmasi Hapus](tests/screenshots/05-modal-konfirmasi-hapus.png)

### 6. Halaman 404 Not Found
Halaman penanganan rute tidak ditemukan yang informatif dan dilengkapi tombol kembali ke halaman beranda.
![Halaman 404 Not Found](tests/screenshots/06-halaman-404.png)

---

## 🔍 Penjelasan Rinci Implementasi Kriteria Tugas

### 1. Database `inventaris_db` dengan 3 Tabel Relasional & Foreign Key
Struktur database dirancang secara normalisasi dengan 3 tabel utama di [`database/migrations/schema.sql`](database/migrations/schema.sql):
- **`categories`**: Menyimpan kategori produk (`id`, `name`).
- **`suppliers`**: Menyimpan distributor pemasok produk (`id`, `name`, `email`, `phone`, `address`).
- **`products`**: Menyimpan entitas produk (`id`, `category_id`, `supplier_id`, `name`, `sku`, `price`, `stock`, `description`, `discount_percentage`, `rating`, `thumbnail`, timestamps).
- **Integritas Relasi (Foreign Key)**:
  - `CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE`
  - `CONSTRAINT fk_products_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE`

### 2. Seeding Data (Minimal 5 Data Seed per Tabel)
Sistem dilengkapi modul pengisian data otomatis di [`database/Seeder/Seeder.php`](database/Seeder/Seeder.php) yang dapat dijalankan lewat `composer db:seed` atau `composer db:migrate:fresh -- --seed`:
- **`categories`**: 8 kategori produk komprehensif (Elektronik, Pakaian, Peralatan Rumah Tangga, Makanan, dsb.).
- **`suppliers`**: 10 entitas pemasok acak realistis lokal Indonesia menggunakan **Faker PHP** (`id_ID`) lengkap dengan profil perusahaan, email, nomor telepon, dan alamat kantor.
- **`products`**: 30+ produk lengkap dengan variasi harga, stok, diskon, rating, deskripsi, thumbnail, serta keterkaitan foreign key yang valid.

### 3. Koneksi PDO dengan Pola Singleton (Singleton Pattern)
Koneksi database dikelola menggunakan pola desain **Singleton** pada class [`Database\Database`](database/Database.php) dan [`database/pdo.php`](database/pdo.php):
- **Constructor dan Clone Privat**: Mencegah instansiasi langsung melalui operator `new Database()` dan mencegah penggandaan objek melalui `clone`.
- **Instance Tunggal**: Variabel statis privat `private static ?PDO $instance = null;` menyimpan koneksi PDO tunggal.
- **Akses Global `Database::getInstance()`**: Memastikan koneksi database hanya dibuka satu kali selama siklus request PHP berlangsung, menghemat resource server dan mencegah *connection leaking*.
- Teruji secara otomatis dengan PHPUnit pada [`tests/DatabaseSingletonTest.php`](tests/DatabaseSingletonTest.php).

### 4. Halaman List Produk dengan 2 JOIN Sekaligus
Pada halaman tabel inventaris `/products`, data ditarik dari repository [`ProductRepository::paginate()`](app/Repositories/ProductRepository.php) yang menggabungkan 3 tabel melalui 2 operasi `LEFT JOIN`:
```sql
SELECT 
    p.*,
    c.name AS category_name,
    s.name AS supplier_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN suppliers s ON p.supplier_id = s.id
ORDER BY p.id DESC
LIMIT :limit OFFSET :offset;
```
Hasil query ini memungkinkan antarmuka tabel menyajikan nama kategori dan nama supplier secara instan tanpa masalah *N+1 query problem*.

### 5. Form Tambah Produk dengan Dropdown Kategori & Supplier Dinamis
Pada halaman `/products/create` ([`resources/views/products/create.php`](resources/views/products/create.php)):
- Kategori dan supplier diambil dari database melalui `CategoryRepository` dan `SupplierRepository`.
- Disajikan dalam bentuk elemen `<select>` HTML:
  - `<select name="category_id">` untuk memilih kategori produk.
  - `<select name="supplier_id">` untuk memilih supplier/distributor penyuplai.
- Dilengkapi mekanisme *re-populating* nilai lama (`$old['category_id']`) jika formulir gagal divalidasi.

### 6. Fitur Update (Form Pre-filled) & Delete (Modal Konfirmasi)
- **Update**: Halaman `/products/edit?id=...` ([`resources/views/products/edit.php`](resources/views/products/edit.php)) secara otomatis membaca data produk berdasarkan ID dan mengisi seluruh input (*pre-filled*), termasuk nama, SKU, harga, stok, diskon, rating, deskripsi, thumbnail, serta menandai kategori & supplier yang sedang terpilih.
- **Delete**: Tombol hapus tidak langsung menghapus data. Tombol memicu modal dialog konfirmasi interaktif di tengah layar (*centered modal*) dengan efek redup *backdrop blur*. Penghapusan hanya diproses jika pengguna mengonfirmasi via tombol "Ya, Hapus Produk".

### 7. Penggunaan Prepared Statements pada Seluruh Query SQL
Semua query yang berhubungan dengan input pengguna pada repository [`app/Repositories/`](app/Repositories/) menggunakan **PDO Prepared Statements**:
- Menggunakan placeholder `:param` (misal `:id`, `:name`, `:sku`, `:price`, `:search_name`).
- Menggunakan metode `$stmt->bindValue()` dengan tipe parameter eksplisit (`PDO::PARAM_STR`, `PDO::PARAM_INT`).
- Menonaktifkan emulasi prepared statements (`PDO::ATTR_EMULATE_PREPARES => false`) agar validasi tipe data dieksekusi langsung oleh engine MySQL.

### 8. Sanitasi Output HTML Menggunakan `htmlspecialchars()`
Untuk mencegah celah keamanan **Cross-Site Scripting (XSS)**, semua data variabel dinamis yang dicetak ke view di-escape menggunakan fungsi global `e()` yang didefinisikan di [`app/Vite/helpers.php`](app/Vite/helpers.php):
```php
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
```
Fungsi ini mengubah karakter khusus seperti `<`, `>`, `&`, `"`, dan `'` menjadi entitas HTML yang aman.

### 9. Flash Message Sukses & Gagal Menggunakan Pola Redirect (PRG Pattern)
Aplikasi menerapkan standar arsitektur **Post/Redirect/Get (PRG)**:
- Setelah operasi penambahan, perubahan, atau penghapusan produk diproses oleh `ProductController`, controller membuat pesan flash melalui fungsi `flash('success', ...)` atau `flash('error', ...)`.
- Controller kemudian mengalihkan browser menggunakan `redirect('/products')` (HTTP 302).
- Di halaman tujuan, pesan flash diambil satu kali dari session, ditampilkan melalui alert banner dan toast notification (*Toastify.js*), lalu dihapus otomatis dari session. Pola ini mencegah terjadinya pengiriman ulang formulir secara tidak sengaja (*form resubmission on refresh*).

### 10. Desain Antarmuka Rapi, Responsif, dan Modern
- Dibangun dengan **Tailwind CSS v4** dan bundler **Vite 8** yang menghasilkan tampilan visual elegan dan responsif.
- Memiliki dukungan tema gelap/terang, tipografi modern, badge status stok (*Ready Stock*, *Stok Menipis*, *Habis*), badge diskon, kartu preview gambar reaktif, dan integrasi ikon vektor **Iconify** (`iconify-icon`).

---

### ⭐ Fitur Bonus yang Diimplementasikan

#### 1. Transaksi Database (ACID) & Audit Logging pada Delete
Penghapusan produk pada [`ProductService::deleteProductWithLog()`](app/Services/ProductService.php) di-wrap dalam transaksi database PDO:
```php
$pdo->beginTransaction();
try {
    // 1. Ambil detail produk sebelum dihapus
    // 2. Eksekusi query DELETE
    // 3. Tulis entri audit ke storage/logs/database.log
    $pdo->commit();
} catch (\Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
```
Setiap transaksi commit akan mencatat detail data yang dihapus (timestamp, action, ID, SKU, nama produk, harga, stok, kategori, supplier) ke berkas [`storage/logs/database.log`](storage/logs/database.log). Jika pencatatan log gagal, seluruh transaksi di-*rollback* sehingga data di database tidak terhapus.

#### 2. Fitur Pencarian Multi-Kolom
Pencarian instan pada tabel manajemen produk mencakup nama produk, SKU, nama kategori, dan nama supplier sekaligus dengan memanfaatkan prepared statement parameter wildcard.

#### 3. Pagination Server-Side & Navigasi Halaman
Dukungan pagination pada tabel manajemen produk dan katalog belanja publik, menghitung total baris dan halaman secara dinamis (`LIMIT :limit OFFSET :offset`).

#### 4. Ekspor Laporan CSV Asinkron (Fetch API + League CSV)
Tombol **Export Laporan** di halaman `/products` memicu pengunduhan CSV secara asinkron via Fetch API menggunakan library **League CSV** (`league/csv`). Halaman tidak mengalami reload, tombol otomatis masuk ke status *disabled*, dan ikon berubah menjadi spinner loading selama file CSV disiapkan.

#### 5. Sistem Reset Database Mandiri Terproteksi (UUID v7)
Memungkinkan penguji/demo me-reset database ke kondisi awal secara mandiri tanpa memerlukan akses terminal SSH atau cPanel:
- Skrip Node.js `pnpm run key:generate` ([`scripts/generate-key.js`](scripts/generate-key.js)) menghasilkan token **UUID versi 7** (RFC 9562) ke berkas `.env` (`DB_RESET_KEY`).
- Endpoint `POST /api/db/reset` memverifikasi kunci menggunakan perbandingan string aman (*timing-safe comparison* `hash_equals()`).
- Jika kunci cocok, server mengeksekusi migrasi fresh dan seeder otomatis. Jika kunci tidak cocok, server mengembalikan respon status `403 Forbidden`.

---

---

## 🛠️ Teknologi yang Digunakan

### Backend & Core
- **PHP 8.2+ / 8.5** — Native PHP dengan struktur MVC berorientasi objek, PSR-4 Autoloading, dan strict typing (`declare(strict_types=1)`).
- **Composer** — Manajemen dependensi PHP dan testing framework.
- **PDO & MySQL / MariaDB** — Database relasional dengan prepared statements dan transaksi ACID.
- **League CSV (`league/csv`)** — Library profesional pembuatan dan pemrosesan stream file CSV.
- **Faker (`fakerphp/faker`)** — Generator data acak realistis lokal Indonesia (`id_ID`) untuk seeding suppliers.
- **vlucas/phpdotenv** — Pengelolaan konfigurasi environment `.env`.
- **spatie/fork** — Eksekusi proses concurrent server PHP dan dev bundler Vite secara simultan.

### Frontend
- **Tailwind CSS v4** — Utility-first styling engine modern berbasis `@theme` token tanpa dependensi CSS kuno.
- **Vite 8** — Next-generation frontend tooling dengan Fast HMR dan asset manifest pipeline.
- **Iconify (`iconify-icon`)** — Ikon vektor ringan dan performan tinggi.
- **Toastify.js** — Notifikasi toast interaktif.

### Quality Assurance & Testing
- **PHPUnit 11** — Pengujian otomatis unit dan integrasi (38 tests, 89 assertions).
- **Playwright** — End-to-End (E2E) automated browser testing dan visual regression/screenshot generator pada mode desktop.

---

## 🧪 Pengujian Otomatis (Automated Testing)

Proyek ini dilengkapi dua tingkat pengujian otomatis untuk menjamin keandalan sistem:

### 1. Menjalankan Unit Testing PHPUnit

Jalankan pengujian unit melalui Composer:

```bash
composer test
# atau melalui binary PHPUnit langsung:
./vendor/bin/phpunit --colors=always
```

Ringkasan hasil pengujian:
```text
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.0
Configuration: /path/to/phpunit.xml

......................................                            38 / 38 (100%)

Time: 00:00.027, Memory: 10.00 MB

OK (38 tests, 89 assertions)
```

Suite pengujian mencakup:
- [`tests/DatabaseSingletonTest.php`](tests/DatabaseSingletonTest.php): Pengujian pola desain Singleton koneksi PDO (`getInstance()`, private constructor & `__clone`).
- [`tests/HelpersTest.php`](tests/HelpersTest.php): Pengujian fungsi sanitasi HTML `e()` dan helper formatting rupiah `format_rupiah()`.
- [`tests/ProductModelTest.php`](tests/ProductModelTest.php): Pengujian instansiasi model `Product`, parsing array, dan serialisasi data.
- [`tests/DatabaseResetServiceTest.php`](tests/DatabaseResetServiceTest.php): Pengujian validasi kunci rahasia `DB_RESET_KEY` (timing-safe check) dan penanganan input tidak sah.
- [`tests/ViteTest.php`](tests/ViteTest.php): Pengujian Vite tag generator, asset manifest resolver, dan dev mode detection.

### 2. Menjalankan E2E Testing & Screenshot Generator (Playwright)

Jalankan pengujian E2E browser untuk memverifikasi fungsionalitas dan memperbarui file tangkapan layar di `tests/screenshots/`:

```bash
pnpm run test:e2e
```

Hasil pengujian otomatis tersimpan di direktori `tests/screenshots/`:
- `01-katalog-home.png`
- `02-manajemen-produk.png`
- `03-tambah-produk.png`
- `04-edit-produk.png`
- `05-modal-konfirmasi-hapus.png`
- `06-halaman-404.png`

---

## 🚀 Panduan Menjalankan Proyek Secara Lokal

### 1. Prasyarat Sistem
- **PHP** 8.2 atau lebih baru (dengan ekstensi `pdo_mysql`, `mbstring`, `curl`)
- **MySQL** atau **MariaDB** server aktif
- **Composer** 2.x
- **Node.js** 20 atau lebih baru
- **pnpm** (atau npm)

### 2. Instalasi Dependensi
Clone repository dan pasang seluruh paket dependensi:

```bash
git clone https://github.com/Realitaa/TugasWeb-Pertemuan8-CRUD.git
cd TugasWeb-Pertemuan8-CRUD

# Install dependensi frontend & backend
pnpm install
composer install
```

### 3. Konfigurasi Environment & Key Generation
Salin template konfigurasi `.env`:

```bash
cp .env.example .env
```

Sesuaikan kredensial koneksi database pada `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).  
Kemudian buat kunci reset database baru:

```bash
pnpm run key:generate
```

### 4. Migrasi Database & Seeding
Jalankan migrasi tabel dan pengisian data contoh:

```bash
# Menjalankan migrasi fresh beserta seeder
composer db:migrate:fresh -- --seed
```

### 5. Menjalankan Server Pengembangan (Dev Mode)
Jalankan dev server concurrent (menjalankan server PHP dan Vite sekaligus):

```bash
composer dev
```

Buka peramban Anda pada tautan: **`http://localhost:8000`**

### 6. Build Produksi (Production Build)
Untuk melakukan kompilasi aset statis siap rilis ke folder `dist/`:

```bash
pnpm run build
```

---

## 📁 Struktur Direktori Proyek

```text
├── app/
│   ├── Controllers/
│   │   ├── DatabaseResetController.php   # Handler endpoint reset database (POST)
│   │   └── ProductController.php         # Handler halaman katalog, tabel, form, & CRUD
│   ├── Models/
│   │   ├── Category.php                  # Model entitas Kategori
│   │   ├── Product.php                   # Model entitas Produk
│   │   └── Supplier.php                  # Model entitas Pemasok/Supplier
│   ├── Repositories/
│   │   ├── CategoryRepository.php        # Query database tabel categories
│   │   ├── ProductRepository.php         # Query CRUD produk, pagination, & 2 JOINs
│   │   └── SupplierRepository.php        # Query database tabel suppliers
│   ├── Services/
│   │   ├── DatabaseResetService.php      # Layanan validasi key & migrasi fresh + seed
│   │   └── ProductService.php            # Validasi input, log transaksi, & ekspor CSV
│   └── Vite/
│       ├── Vite.php                      # Adapter tag injector Vite
│       └── helpers.php                   # Fungsi global e(), format_rupiah(), view(), dll.
├── bin/
│   ├── dev.php                           # Skrip concurrent runner PHP & Vite
│   ├── fresh.php                         # CLI runner composer db:migrate:fresh
│   ├── migrate.php                       # CLI runner composer db:migrate
│   └── seed.php                          # CLI runner composer db:seed
├── config/
│   └── database.php                      # Konfigurasi koneksi database MySQL/SQLite
├── database/
│   ├── migrations/
│   │   └── schema.sql                    # Skema DDL tabel categories, suppliers, products
│   ├── Database.php                      # Implementasi Singleton pattern koneksi PDO
│   ├── pdo.php                           # Adapter pemanggil Database::getInstance()
│   └── Seeder/
│       └── Seeder.php                    # Seeder otomatis kategori, supplier, & produk
├── resources/
│   ├── css/
│   │   └── app.css                       # Styling Tailwind CSS v4 & custom theme
│   ├── js/
│   │   ├── app.js                        # Entrypoint JS aplikasi
│   │   ├── pagination.js                 # Helper komponen pagination
│   │   ├── products.js                   # Client-side renderer katalog belanja
│   │   └── utils.js                      # Helper formatting rupiah & status waktu
│   └── views/
│       ├── layouts/
│       │   └── app.php                   # Main layout HTML, meta tags, & navbar
│       ├── products/
│       │   ├── create.php                # View formulir tambah produk & live preview
│       │   ├── edit.php                  # View formulir edit produk
│       │   └── index.php                 # View tabel daftar produk & modal konfirmasi
│       ├── 404.php                       # View halaman 404 Not Found
│       └── home.php                      # View katalog belanja utama
├── scripts/
│   ├── fetch-products.js                 # Skrip penarik data dari DummyJSON
│   └── generate-key.js                   # Generator UUID v7 & pengisi DB_RESET_KEY .env
├── storage/
│   └── logs/
│       └── database.log                  # Log transaksi database (ACID audit trail)
├── tests/
│   ├── e2e/
│   │   └── screenshots.spec.js           # Playwright E2E & Desktop Screenshot test
│   ├── screenshots/                      # Direktori penyimpanan tangkapan layar desktop
│   │   ├── 01-katalog-home.png
│   │   ├── 02-manajemen-produk.png
│   │   ├── 03-tambah-produk.png
│   │   ├── 04-edit-produk.png
│   │   ├── 05-modal-konfirmasi-hapus.png
│   │   └── 06-halaman-404.png
│   ├── DatabaseResetServiceTest.php      # Unit test validasi key reset database
│   ├── DatabaseSingletonTest.php         # Unit test pola Singleton koneksi PDO
│   ├── HelpersTest.php                   # Unit test fungsi e() & format_rupiah()
│   ├── ProductModelTest.php              # Unit test model Product
│   └── ViteTest.php                      # Unit test Vite adapter
├── index.php                             # Router utama aplikasi web
├── playwright.config.js                  # Konfigurasi Playwright E2E Desktop
├── composer.json                         # Dependensi PHP & composer scripts
├── package.json                          # Dependensi frontend & node scripts
└── vite.config.js                        # Konfigurasi bundler Vite 8
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
