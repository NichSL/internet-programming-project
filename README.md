# Internet Programming Project — MarketHub

Aplikasi web berbasis **Laravel 13** dengan autentikasi multi-role (`admin`, `seller`, dan `user`), manajemen katalog produk, keranjang belanja, checkout, dan riwayat pesanan. Project ini juga menyediakan REST API menggunakan Laravel Sanctum serta siap dideploy ke **Railway**.

---

## 1. Teknologi yang Dibutuhkan

Pastikan perangkat lokal sudah terpasang:

- **PHP 8.4 atau lebih baru** beserta ekstensi (`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `bcmath`).
- **Composer 2.x**.
- **Node.js 22.12+ (LTS)** dan **npm**.
- **MySQL 8.0+** (atau MariaDB / SQLite).
- **Git**.

Periksa instalasi melalui terminal:

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

---

## 2. Instalasi dan Setup Lokal

### Langkah 1: Masuk ke Folder Project
```bash
cd internet-programming-project
```

### Langkah 2: Install Dependency Backend & Frontend
```bash
composer install
npm install
```

### Langkah 3: Konfigurasi File Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan koneksi database MySQL:
```env
APP_NAME=MarketHub
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=internet_programming
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan:** Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan kredensial MySQL lokal Anda.

### Langkah 4: Buat Database MySQL
Jalankan MySQL dan buat database yang sesuai dengan nama di `.env`:
```sql
CREATE DATABASE internet_programming;
```

### Langkah 5: Generate Application Key
```bash
php artisan key:generate
```

### Langkah 6: Jalankan Database Migration
Jalankan migrasi untuk membuat seluruh tabel:
```bash
php artisan migrate
```

### Langkah 7: Jalankan Database Seeders
Jalankan seeder untuk mengisi akun default, sample produk katalog, dan sample transaksi:
```bash
php artisan db:seed
```

Atau gabungkan langkah migrasi dan seeder:
```bash
php artisan migrate --seed
```

> **Aman Dijalankan Berulang:** Seeder menggunakan logika `firstOrCreate`, sehingga aman dijalankan berkali-kali (`php artisan db:seed`) tanpa menimbulkan duplikasi data atau mereset data akun yang sudah ada.

### Langkah 8: Menjalankan Aplikasi
Buka **dua terminal** terpisah pada direktori project:

**Terminal 1 — Server Laravel:**
```bash
php artisan serve
```
Aplikasi berjalan di: `http://127.0.0.1:8000`

**Terminal 2 — Vite Asset Compiler:**
```bash
npm run dev
```

---

## 3. Akun Login Tester & Demo

Setelah menjalankan seeder (`php artisan db:seed`), akun demo berikut siap digunakan:

| Role | Username | Password | Toko / Deskripsi | Halaman Login |
|---|---|---|---|---|
| **Admin** | `admin_niko` | `adminniko123` | Administrator Sistem | `/admin/login` |
| **Seller** | `seller_budi` | `12345678` | Toko Elektronik Jaya | `/login` |
| **Seller** | `seller_siti` | `12345678` | Siti Fashion Corner | `/login` |
| **User (Buyer)** | `Akuganteng123` | `12345678` | Pembeli (dengan sample riwayat pesanan & keranjang) | `/login` |
| **User (Buyer)** | `qwe` | `12345678` | Pembeli (dengan sample pesanan) | `/login` |

> **Catatan Autentikasi:**
> - Akun **Admin** wajib login melalui portal khusus `/admin/login` (validasi format username mengandung kata `admin`).
> - Akun **Seller** dan **User biasa** login melalui portal `/login`.
> - Seller yang berhasil login akan otomatis diarahkan ke menu dashboard penjual untuk mengelola produk.

---

## 4. Halaman & Fitur Aplikasi

| Halaman | URL | Akses Role | Keterangan |
|---|---|---|---|
| Halaman Awal | `/` | Publik | Otomatis mengarahkan ke halaman login |
| Login User & Seller | `/login` | Guest | Autentikasi user dan penjual |
| Registrasi Akun | `/register` | Guest | Pendaftaran pembeli baru |
| Login Khusus Admin | `/admin/login` | Guest | Portal khusus pengelola sistem |
| Katalog & Belanja | `/dashboard` | User, Seller | Melihat katalog, pencarian produk, keranjang, beli instan |
| Keranjang & Checkout | `/checkout` | User, Seller | Review item keranjang belanja dan proses checkout |
| Riwayat Pesanan | `/orders` | User, Seller | Daftar riwayat pembelian user |
| Upgrade Jadi Seller | `/become-seller` | User | Mendaftarkan nama dan alamat toko baru |
| Dashboard Penjual | `/seller/dashboard` | Seller | Manajemen produk toko (Tambah, Edit, Hapus) |
| Dashboard Admin | `/admin/dashboard` | Admin | Statistik pengguna, daftar toko, dan monitoring sistem |
| Detail Pengguna | `/admin/user/{id}` | Admin | Melihat profil dan history transaksi pembeli |
| Detail Toko | `/admin/store/{id}` | Admin | Melihat katalog dan riwayat penjualan toko |
| Pengaturan Akun | `/settings` | Auth (Semua) | Ubah profil, password, dan informasi toko |

---

## 5. REST API (Laravel Sanctum)

Base URL saat server lokal berjalan: `http://127.0.0.1:8000/api`

### Endpoint Utama

| Method | Endpoint | Auth | Keterangan |
|---|---|---|---|
| `POST` | `/api/login` | Publik | Login user/seller dan mendapatkan API Bearer Token |
| `POST` | `/api/admin/login` | Publik | Login admin dan mendapatkan Token |
| `GET` | `/api/user` | Bearer Token | Mendapatkan detail profil akun yang sedang login |
| `GET` | `/api/market` | Bearer Token | Melihat seluruh katalog produk aktif |
| `GET` | `/api/market/{id}` | Bearer Token | Melihat detail satu produk spesifik |
| `GET` | `/api/products` | Bearer Token | Endpoint resource produk seller |
| `GET` | `/api/cart` | Bearer Token | Mengambil daftar item keranjang belanja user |
| `POST` | `/api/cart` | Bearer Token | Menambahkan produk ke keranjang belanja |
| `PATCH` | `/api/cart/{id}` | Bearer Token | Mengubah kuantitas item keranjang |
| `DELETE` | `/api/cart/{id}` | Bearer Token | Menghapus item dari keranjang |
| `DELETE` | `/api/cart` | Bearer Token | Mengosongkan keranjang belanja |
| `GET` | `/api/orders/my-orders` | Bearer Token | Mengambil riwayat pesanan milik akun yang login |
| `GET` | `/api/orders/{id}` | Bearer Token | Mengambil detail spesifik pesanan milik akun |
| `GET` | `/api/admin/users` | Admin Token | Mengambil seluruh daftar pengguna |
| `GET` | `/api/admin/user/{id}/history` | Admin Token | Melihat riwayat belanja user tertentu |
| `GET` | `/api/admin/store/{id}/history` | Admin Token | Melihat riwayat penjualan toko tertentu |

---

## 6. Deployment ke Railway

Project ini telah dikonfigurasi penuh untuk deployment di **Railway** menggunakan multi-stage **Dockerfile** berbasis PHP 8.4-FPM, Nginx, dan Node 22 LTS.

### A. Konfigurasi Environment Variables di Railway
Pada dashboard project Railway Anda (**Variables** tab), tambahkan variabel berikut:

| Variable | Rekomendasi Nilai | Keterangan |
|---|---|---|
| `APP_NAME` | `MarketHub` | Nama aplikasi |
| `APP_ENV` | `production` | Mode production |
| `APP_KEY` | *(generate via `php artisan key:generate --show`)* | Kunci enkripsi Laravel (32 karakter base64) |
| `APP_DEBUG` | `false` | Matikan debug stack trace demi keamanan |
| `APP_URL` | `https://${{RAILWAY_PUBLIC_DOMAIN}}` | Domain publik Railway Anda |
| `DB_CONNECTION` | `mysql` | Tipe database (`mysql`, `pgsql`, atau `sqlite`) |
| `SESSION_DRIVER` | `database` | Penyimpanan session di database |
| `CACHE_STORE` | `database` | Cache driver |
| `FILESYSTEM_DISK` | `local` | Storage disk |
| `RUN_MIGRATIONS` | `true` | Otomatis menjalankan `php artisan migrate --force` |
| `RUN_SEEDERS` | `false` (atau `true`) | Set ke `true` jika ingin seeder otomatis jalan saat deploy |

> **Koneksi Database Railway:**
> Jika Anda menambahkan plugin **MySQL** atau **PostgreSQL** di Railway, Railway akan otomatis menginjeksi variabel `MYSQL_URL`, `DATABASE_URL`, `MYSQLHOST`, dll. File `config/database.php` pada project ini telah dirancang untuk membaca variabel tersebut secara otomatis.

### B. Eksekusi Migration dan Seeder di Railway

1. **Migrasi Otomatis:**
   Script startup container (`docker/entrypoint.sh`) otomatis menjalankan:
   ```bash
   php artisan migrate --force --no-interaction
   ```
   Perintah ini non-destruktif dan tidak akan menghapus data yang sudah ada.

2. **Seeding Otomatis saat Deploy:**
   Untuk menjalankan seeder otomatis saat container dinyalakan pertama kali, atur environment variable:
   ```env
   RUN_SEEDERS=true
   ```
   Karena seeder menggunakan `firstOrCreate`, redeployment di masa mendatang tidak akan menduplikasi atau menimpa data.

3. **Seeding Manual via Railway CLI:**
   Jika lebih memilih menjalankan seeder secara manual setelah deploy:
   ```bash
   railway run php artisan db:seed --force
   ```
   Atau buka tab **Exec / Console** di dashboard Railway service Anda dan jalankan `php artisan db:seed --force`.

---

## 7. Troubleshooting

**A. Gagal terhubung ke MySQL (`Connection refused`)**
- Pastikan MySQL server lokal aktif.
- Periksa `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`.
- Jalankan `php artisan config:clear`.

**B. `No application encryption key has been specified`**
```bash
php artisan key:generate
```

**C. Membersihkan Cache Aplikasi dan Rute**
```bash
php artisan optimize:clear
```

**D. Jalankan Pengujian Otomatis (Pest Tests)**
```bash
php artisan test
```
