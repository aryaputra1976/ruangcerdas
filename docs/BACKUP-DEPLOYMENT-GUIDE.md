# Backup & Deployment Guide - Ruang Cerdas

## A. Tujuan Dokumen

Panduan teknis backup, restore, dan deployment aplikasi Ruang Cerdas ke hosting production.

## B. Komponen yang Wajib Dibackup

- Database (MySQL/MariaDB atau SQLite sesuai environment).
- File `.env` production.
- `storage/app/private` (atau lokasi file produk private yang dipakai project).
- `storage/app/private/products` wajib dibackup karena berisi file digital utama per produk.
- `storage/app/public` (termasuk payment proof upload jika dipakai).
- `public/build` (hasil build frontend Vite).
- `composer.lock` dan `package-lock.json`.
- Dokumentasi di folder `docs`.

Catatan:
- `public/storage` adalah symlink, tidak perlu dibackup karena bisa dibuat ulang.

## C. Komponen yang Tidak Wajib Dibackup

- `vendor/` (bisa dibuat ulang dengan Composer).
- `node_modules/` (bisa dibuat ulang dengan npm).
- `storage/framework/cache`.
- `storage/framework/views`.
- `storage/logs` (opsional, simpan jika perlu audit/troubleshooting).
- `bootstrap/cache` (hasil cache dapat dibuat ulang).

## D. Struktur Folder Production yang Disarankan

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
|- composer.json
|- composer.lock
|- package.json
|- package-lock.json
`- .env
```

Document root domain/subdomain:

```text
ruangcerdas/public
```

## E. Deployment dari GitHub ke Server/VPS

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## F. Deployment ke Shared Hosting (Tanpa SSH Penuh)

- Build frontend di lokal (`npm ci && npm run build`).
- Jalankan `composer install` di lokal jika hosting tidak mendukung Composer.
- Upload source Laravel ke folder aplikasi, bukan langsung semua isi ke `public_html`.
- Arahkan document root subdomain ke folder `public` jika memungkinkan.
- Jika tidak bisa ubah document root, lakukan setup sangat hati-hati dan pastikan `.env`/source tidak terbuka publik.
- Upload `public/build`.
- Upload `vendor` jika server tidak bisa `composer install`.
- Import database via phpMyAdmin.
- Edit `.env` production.
- Jalankan migration jika ada akses terminal/cron/fitur artisan.
- Jika tidak ada terminal, gunakan database hasil migrate dari lokal/staging.

## G. Backup Database MySQL

Contoh backup:

```bash
mysqldump -u USER -p DATABASE > backup_ruangcerdas_YYYYMMDD.sql
```

Contoh restore:

```bash
mysql -u USER -p DATABASE < backup_ruangcerdas_YYYYMMDD.sql
```

Alternatif phpMyAdmin:

- Export database.
- Pilih format SQL.
- Import file SQL saat restore.

## H. Backup SQLite (Lokal/Staging)

- Backup file `database/database.sqlite`.
- Jangan overwrite saat aplikasi aktif menulis data.
- Simpan salinan bertanggal.

## I. Backup Storage

Backup minimal:

- Folder produk private.
- Folder payment proof.
- Folder upload public lain jika ada.

Contoh nama arsip:

```text
storage_ruangcerdas_YYYYMMDD.zip
```

## J. Restore Production

Urutan restore:

1. Aktifkan maintenance mode jika memungkinkan.
2. Backup kondisi server saat ini.
3. Restore database.
4. Restore storage.
5. Restore `.env`.
6. Jalankan `composer install` jika perlu.
7. Jalankan `npm run build` jika perlu.
8. Jalankan `php artisan optimize:clear`.
9. Jalankan cache ulang (`config:cache`, `route:cache`, `view:cache`, `optimize`).
10. Matikan maintenance mode.
11. Uji checkout sampai download.

## K. Maintenance Mode

```bash
php artisan down
php artisan up
```

Catatan:
- Hindari maintenance terlalu lama saat jam transaksi aktif.

## L. Checklist Setelah Deployment

- Homepage tampil.
- Produk tampil.
- Checkout berhasil.
- Kupon valid/invalid berfungsi sesuai aturan.
- Payment setting benar.
- Upload bukti bayar berhasil.
- Admin approve berhasil.
- Email download link terkirim.
- Secure download berhasil.
- Reports tampil.
- `sitemap.xml` tampil.
- `robots.txt` tampil.
- `APP_DEBUG=false`.

## M. Rollback Sederhana

- Simpan backup sebelum deploy.
- Gunakan commit sebelumnya (`git checkout <commit>`) jika pakai Git.
- Restore database backup jika migration bermasalah.
- Restore storage jika file upload hilang.
- Jalankan `php artisan optimize:clear` setelah rollback.

## N. Catatan Keamanan

- Jangan letakkan `.env` di area public.
- Jangan simpan file ZIP produk di folder public.
- Jangan taruh backup SQL di folder public.
- Batasi akses admin.
- Gunakan password kuat.
- Hapus file backup dari server publik setelah selesai diunduh.
- Jangan commit `.env` ke GitHub.

## O. Jadwal Backup yang Disarankan

- Database: harian atau mingguan (sesuai volume transaksi).
- Storage produk: setiap ada produk baru atau update besar.
- Payment proof: mingguan.
- `.env`: setiap ada perubahan konfigurasi.
- Backup lengkap: sebelum deploy besar.

## P. Final Acceptance

Deployment dianggap aman jika:

- Backup tersedia.
- Proses restore dipahami tim.
- Checkout sampai secure download berhasil.
- Email terkirim.
- `APP_DEBUG=false`.
- Tidak ada file rahasia di area public.
- `sitemap.xml` dan `robots.txt` tampil normal.
