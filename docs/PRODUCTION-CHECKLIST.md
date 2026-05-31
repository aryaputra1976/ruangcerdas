# Production Checklist - Ruang Cerdas

Dokumen ini adalah checklist deploy production untuk aplikasi Ruang Cerdas.

## A. Persiapan Server/Hosting

- PHP versi minimal sesuai kebutuhan Laravel 12 (disarankan PHP 8.2+).
- Ekstensi PHP umum Laravel tersedia:
  - `bcmath`
  - `ctype`
  - `fileinfo`
  - `json`
  - `mbstring`
  - `openssl`
  - `pdo`
  - `pdo_mysql` (atau driver DB yang dipakai)
  - `tokenizer`
  - `xml`
  - `curl`
  - `zip`
- MySQL/MariaDB siap pakai.
- Composer tersedia di server (atau build `vendor` di lokal jika shared hosting tidak mendukung Composer).
- Node/npm tersedia jika build asset dilakukan di server (opsional, bisa build di lokal).
- Domain/subdomain diarahkan ke folder `public`.

## B. File dan Folder yang Perlu Diupload

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/` (jika hosting tidak bisa `composer install`)
- `artisan`
- `composer.json`
- `composer.lock`
- `package.json`
- `package-lock.json` (jika ada)
- hasil build frontend: `public/build/`

## C. File/Folder yang Jangan Dibuka Publik

- `.env` tidak boleh bisa diakses publik.
- File produk digital private jangan ditaruh di folder public.
- File ZIP produk tetap di storage private (contoh: `storage/app/private/products` atau struktur private project saat ini).
- Jangan jadikan source Laravel penuh sebagai document root (`public_html`).
- Document root harus mengarah ke folder `public` saja.

## D. Contoh `.env` Production

Gunakan nilai sesuai server production:

```env
APP_NAME="Ruang Cerdas"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=false
APP_URL=https://ruangcerdas.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ruangcerdas
DB_USERNAME=ruangcerdas_user
DB_PASSWORD=strong_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@ruangcerdas.id
MAIL_FROM_NAME="Ruang Cerdas"

QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
SESSION_DRIVER=database
CACHE_STORE=database
```

## E. Command Deployment

Jalankan sesuai urutan (sesuaikan bila build dilakukan di lokal):

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan test
```

Catatan:
- `php artisan key:generate` hanya jika `APP_KEY` belum ada.
- `php artisan test` dijalankan jika environment server memungkinkan.

## F. Checklist Fitur Bisnis

- Produk aktif tampil di halaman public.
- Checkout berhasil.
- Invoice/thank-you page tampil.
- Payment setting tampil.
- Upload bukti bayar berhasil.
- Admin approve pembayaran berhasil.
- Email download link terkirim.
- Secure download token bisa dipakai.
- Token expired tidak bisa dipakai.
- Kupon valid bisa dipakai.
- Kupon invalid menampilkan error.
- Reports tampil normal.
- `/sitemap.xml` tampil.
- `/robots.txt` tampil.

## G. Checklist Keamanan

- `APP_DEBUG=false`.
- Admin menggunakan password kuat.
- Register publik dimatikan jika tidak dibutuhkan bisnis.
- Folder storage private tidak dapat diakses langsung.
- Download hanya lewat secure token.
- Route admin terlindungi middleware auth.
- Upload payment proof dibatasi validasi file.
- Backup database berjalan.
- File `.env` tidak bisa diakses publik.
- Permission folder `storage/` dan `bootstrap/cache/` benar untuk web server.

## H. Checklist Email

- Test kirim email berhasil.
- `MAIL_FROM_ADDRESS` sesuai domain valid.
- Cek folder spam/promotions.
- Cek link download pada email benar dan bisa dibuka.

## I. Checklist Backup

- Backup database.
- Backup storage produk private.
- Backup payment proof.
- Backup `.env` secara aman (encrypted/secret manager).
- Jadwal backup mingguan atau sesuai kebijakan operasional.

## J. Troubleshooting Singkat

### 1) 500 Error
- Cek `storage/logs/laravel.log`.
- Cek `APP_KEY`, kredensial DB, permission folder.
- Jalankan ulang cache command bila perlu.

### 2) 404 Route
- Cek web server rewrite (`.htaccess`/Nginx config).
- Jalankan `php artisan route:clear` lalu `php artisan route:cache`.

### 3) CSS/JS Tidak Muncul
- Pastikan `npm run build` sudah dijalankan.
- Pastikan folder `public/build` terupload.
- Cek referensi Vite manifest.

### 4) Gambar/Storage Tidak Muncul
- Jalankan `php artisan storage:link`.
- Cek permission storage.
- Cek path file di DB.

### 5) Email Tidak Terkirim
- Verifikasi konfigurasi `MAIL_*`.
- Cek port/encryption SMTP.
- Cek log aplikasi.

### 6) Migrate Gagal
- Cek kredensial DB dan hak akses user DB.
- Jalankan migration perbaikan setelah sinkron schema.

### 7) Permission Denied
- Cek owner/group folder project.
- Beri write access untuk `storage/` dan `bootstrap/cache/`.

### 8) Route Cache Menyebabkan Route Lama
- Jalankan:
  - `php artisan route:clear`
  - `php artisan route:cache`

## Struktur Hosting yang Disarankan

```text
ruangcerdas/
|- app/
|- bootstrap/
|- config/
|- database/
|- public/
|- resources/
|- routes/
|- storage/
|- vendor/
|- artisan
`- .env
```

Document root domain/subdomain harus diarahkan ke:

```text
ruangcerdas/public
```

## Catatan Shared Hosting

- Prioritaskan subdomain/domain yang bisa diarahkan langsung ke folder `public`.
- Jika hosting tidak mengizinkan set document root, gunakan pendekatan hati-hati:
  - tetap usahakan source Laravel berada di luar folder web-public.
  - jangan expose seluruh source ke `public_html`.
- Bila terpaksa, konsultasikan dulu ke support hosting untuk setup Laravel yang aman.

## Final Acceptance (Siap Production)

Production dianggap siap jika:

- `php artisan test` hijau.
- `npm run build` hijau.
- `APP_DEBUG=false`.
- Flow checkout sampai secure download berhasil end-to-end.
- Email download terkirim.
- `/sitemap.xml` dan `/robots.txt` bisa diakses.
- Backup awal (database + file penting) sudah dibuat.
