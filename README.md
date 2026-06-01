# Ruang Cerdas

Aplikasi Laravel 12 untuk penjualan produk digital dan belajar online dengan alur checkout manual, verifikasi pembayaran, dan secure download.

## Ringkas Fitur
- Landing + katalog produk public
- Checkout manual per produk
- Upload bukti bayar oleh pembeli
- Verifikasi pembayaran dari admin
- Pengiriman email link download setelah approve
- Secure download dengan token, masa berlaku, dan batas jumlah download
- Sitemap + robots.txt untuk SEO dasar

## Kebutuhan Sistem
- PHP 8.2+
- Composer 2+
- Node.js 20+
- MySQL 8+ atau SQLite

## Setup Lokal Cepat
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install
npm run build
php artisan serve
```

## Konfigurasi Penting Produksi
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` harus domain produksi
- Pastikan konfigurasi `DB_*`, `MAIL_*`, dan filesystem benar

## Perintah Deploy Dasar
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## Verifikasi Sebelum Go Live
- Minimal satu rekening pembayaran aktif sudah diisi
- Halaman thank-you menampilkan instruksi pembayaran valid (tanpa data dummy)
- Upload bukti bayar berjalan
- Admin bisa lihat bukti bayar lewat route admin aman
- Approve order mengirim email link download
- Link download valid sesuai token/expired/max count

## Testing
```bash
php artisan test
```

Dokumen checklist produksi: `docs/PRODUCTION-READINESS-CHECKLIST.md`.
Panduan setup tracking: `docs/TRACKING-SETUP.md`.
Panduan schema markup: `docs/SCHEMA-SETUP.md`.
Catatan optimasi landing: `docs/LANDING-CONVERSION.md`.
