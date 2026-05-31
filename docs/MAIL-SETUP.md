# Mail Setup - Ruang Cerdas

Dokumen ini menjelaskan konfigurasi email untuk fitur pengiriman link download setelah pembayaran order disetujui admin.

## Tujuan Email di Ruang Cerdas

- Mengirim notifikasi profesional ke pembeli saat pembayaran sudah disetujui.
- Memberikan link download produk digital secara aman (berbasis token).
- Mengurangi kebutuhan follow up manual dari admin.

## Kapan Email Dikirim

Email dikirim setelah admin melakukan approve order, yaitu saat status order berubah menjadi `paid` dan data download (`download_token`, `download_expires_at`) sudah tersedia.

## Contoh Konfigurasi `.env` (SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@ruangcerdas.com"
MAIL_FROM_NAME="Ruang Cerdas"
```

## Contoh Mailtrap (Local Testing)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@ruangcerdas.local"
MAIL_FROM_NAME="Ruang Cerdas Local"
```

## Contoh SMTP Production (Umum)

```env
MAIL_MAILER=smtp
MAIL_HOST=your-production-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-production-smtp-username
MAIL_PASSWORD=your-production-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@your-domain.com"
MAIL_FROM_NAME="Ruang Cerdas"
```

Catatan:
- Beberapa provider production menggunakan port `465` dengan `MAIL_ENCRYPTION=ssl`.
- Gunakan domain pengirim yang valid dan terverifikasi sesuai penyedia SMTP.

## Cara Test

1. Jalankan test dasar:
   - `php artisan test`
2. Buat order dari halaman checkout.
3. Upload bukti pembayaran.
4. Approve order dari admin.
5. Cek inbox (Mailtrap/SMTP) atau cek log aplikasi jika email gagal.

## Catatan Keamanan

- Jangan commit username/password SMTP ke repository.
- Simpan kredensial hanya di `.env`.
- Jika email tidak terlihat di inbox, cek folder spam/promosi.
- Pastikan `APP_URL` benar agar domain link download tidak salah.

## Troubleshooting Singkat

### 1) Email tidak masuk

- Cek konfigurasi `MAIL_*` di `.env`.
- Cek konektivitas server ke SMTP host/port.
- Cek log Laravel (`storage/logs/laravel.log`) untuk error detail.

### 2) Link download salah domain

- Pastikan nilai `APP_URL` sesuai domain aplikasi aktif.
- Jalankan ulang config cache bila perlu:
  - `php artisan config:clear`

### 3) SMTP gagal (auth/connection/timeout)

- Validasi ulang `MAIL_USERNAME` dan `MAIL_PASSWORD`.
- Pastikan `MAIL_ENCRYPTION` dan `MAIL_PORT` cocok dengan provider.
- Jika provider butuh app password atau whitelist IP, aktifkan sesuai dokumen provider.
