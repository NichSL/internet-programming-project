# Internet Programming Project

Aplikasi web berbasis **Laravel 13** dengan autentikasi dan pembagian hak akses `admin`, `seller`, dan `user`. Project ini juga menyediakan REST API menggunakan Laravel Sanctum.

## 1. Teknologi yang Dibutuhkan

Pastikan perangkat sudah memiliki:

- PHP **8.3 atau lebih baru** beserta ekstensi PHP yang dibutuhkan Laravel.
- Composer.
- Node.js dan npm.
- MySQL 8.0 (atau versi kompatibel).
- Git (opsional, jika project diambil dari repository).

Periksa instalasi melalui terminal:

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

## 2. Masuk ke Folder Project

Ekstrak project, buka terminal, lalu masuk ke folder project:

```bash
cd internet-programming-project
```

Pastikan terminal berada di folder yang memiliki file `artisan` dan `composer.json`.

## 3. Install Dependency

Jalankan perintah berikut dari folder project:

```bash
composer install
npm install
```

## 4. Buat Database MySQL

Jalankan MySQL, kemudian buat database. Contoh nama database yang digunakan adalah `internet_programming`.

```sql
CREATE DATABASE internet_programming;
```

Bisa dijalankan melalui MySQL CLI, TablePlus, DBeaver, phpMyAdmin, atau aplikasi database lainnya.

## 5. Konfigurasi Environment

Buat file `.env` dari file contoh:

```bash
cp .env.example .env
```

Buka `.env`, kemudian sesuaikan konfigurasi aplikasi dan database. Contoh konfigurasi MySQL:

```env
APP_NAME="Internet Programming Project"
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

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan akun MySQL di komputer masing-masing. Jika MySQL menggunakan password, isi `DB_PASSWORD` dengan password tersebut.

> Pastikan konfigurasi database lain yang sudah ada di `.env` tidak tertinggal menggunakan SQLite. Setelah mengubah `.env`, jalankan `php artisan config:clear`.

## 6. Generate Application Key

```bash
php artisan key:generate
```

## 7. Jalankan Migration dan Seeder

Jalankan:

```bash
php artisan migrate --seed
```

Perintah tersebut membuat tabel database dan memasukkan **tiga akun demo** yang tercantum pada bagian [Akun Login Tester](#10-akun-login-tester).

Seeder menggunakan `updateOrCreate`, sehingga aman dijalankan ulang untuk memperbarui akun demo berdasarkan username. Password disimpan dalam bentuk hash oleh model User.

Jika ingin menghapus seluruh tabel/data lalu membuat ulang database dari awal, gunakan:

```bash
php artisan migrate:fresh --seed
```

**Peringatan:** `migrate:fresh` menghapus seluruh tabel beserta data yang ada di database yang sedang dikonfigurasi. Gunakan hanya pada database development/testing, bukan database yang berisi data penting.

## 8. Jalankan Aplikasi

Buka **dua terminal** dari folder project yang sama.

**Terminal 1 — Laravel:**

```bash
php artisan serve
```

Aplikasi berjalan di:

```text
http://127.0.0.1:8000
```

**Terminal 2 — Vite:**

```bash
npm run dev
```

Biarkan kedua terminal tetap berjalan selama aplikasi digunakan. Vite menyediakan asset frontend selama proses development.

## 9. Halaman Aplikasi

| Halaman | URL | Keterangan |
|---|---|---|
| Halaman awal | `http://127.0.0.1:8000/` | Mengarahkan pengguna ke halaman login user |
| Login User | `http://127.0.0.1:8000/login` | Login untuk akun dengan role `user` atau `seller` |
| Registrasi | `http://127.0.0.1:8000/register` | Membuat akun user baru |
| Login Admin | `http://127.0.0.1:8000/admin/login` | Login khusus akun dengan role `admin` |

**Catatan:** Login menggunakan **username**, bukan alamat email. Akun admin harus masuk melalui halaman Login Admin.

## 10. Akun Login Tester

Gunakan akun berikut untuk mencoba aplikasi setelah menjalankan `php artisan migrate --seed`.

| Role | Username | Password | Halaman Login |
|---|---|---|---|
| Admin | `admin_niko` | `adminniko123` | `/admin/login` |
| User | `Akuganteng123` | `12345678` | `/login` |
| User | `qwe` | `12345678` | `/login` |

Akun demo dibuat otomatis oleh `DatabaseSeeder.php`. Jika akun belum bisa digunakan, jalankan kembali:

```bash
php artisan db:seed
```

Akun-akun ini ditujukan untuk keperluan demo dan pengujian lokal. Jangan gunakan password tersebut untuk deployment atau lingkungan production.

## 11. REST API

Base URL API saat server lokal berjalan:

```text
http://127.0.0.1:8000/api
```

Beberapa endpoint yang tersedia:

| Method | Endpoint | Keterangan |
|---|---|---|
| `POST` | `/api/login` | Login API dan memperoleh token Sanctum |
| `POST` | `/api/admin/login` | Login API khusus admin |
| `GET` | `/api/market` | Melihat katalog produk (memerlukan token) |
| `GET` | `/api/market/{id}` | Melihat detail produk (memerlukan token) |
| `GET` | `/api/user` | Melihat profil akun yang sedang login (memerlukan token) |
| `GET` | `/api/products` | Melihat produk melalui API (memerlukan token) |
| `GET` | `/api/cart` | Melihat keranjang (memerlukan token) |
| `GET` | `/api/orders/my-orders` | Melihat riwayat pesanan sendiri (memerlukan token) |

### Contoh Login API melalui Postman

1. Pilih method `POST`.
2. Masukkan URL `http://127.0.0.1:8000/api/login` untuk user, atau `http://127.0.0.1:8000/api/admin/login` untuk admin.
3. Pilih **Body → raw → JSON**.
4. Kirim username dan password, contoh:

```json
{
  "username": "Akuganteng123",
  "password": "12345678"
}
```

5. Jika berhasil, response akan berisi token. Untuk endpoint yang dilindungi, masukkan token tersebut pada tab **Authorization → Bearer Token**.

## 12. Troubleshooting

**A. `SQLSTATE[HY000] [2002] Connection refused` atau gagal terhubung ke MySQL**

- Pastikan service MySQL sedang berjalan.
- Periksa `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`.
- Pastikan database `internet_programming` sudah dibuat.
- Jalankan `php artisan config:clear` setelah mengubah `.env`.

**B. `No application encryption key has been specified`**

```bash
php artisan key:generate
```

**C. Tabel belum tersedia atau migration belum dijalankan**

```bash
php artisan migrate --seed
```

**D. Perubahan konfigurasi tidak terbaca**

```bash
php artisan optimize:clear
```

**E. Asset/CSS tidak tampil saat development**

Pastikan `npm install` sudah selesai dan jalankan `npm run dev` di terminal terpisah. Pastikan terminal Vite tidak ditutup.

---

## Ringkasan Perintah Menjalankan Project

Setelah konfigurasi `.env` dan database selesai, urutan perintah utama:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Kemudian jalankan pada dua terminal:

```bash
php artisan serve
```

```bash
npm run dev
```

Buka `http://127.0.0.1:8000` di browser.
