# Admin Security Hardening

Dokumen ini merangkum hardening keamanan dasar untuk area admin Ruang Cerdas.

## Prinsip utama

- Semua route admin wajib memakai middleware `auth` dan `admin`.
- Akses admin hanya untuk user dengan role `admin`.
- Tambahkan security headers di area admin untuk perlindungan dasar browser.

## Checklist konfigurasi production

1. Pastikan `APP_DEBUG=false`.
2. Gunakan password admin yang kuat dan unik.
3. Wajib HTTPS di production.
4. Atur session cookie aman via `.env`:

```env
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

5. Jangan share akun admin ke banyak orang.
6. Backup sebelum deploy perubahan.
7. Review `Activity Log` secara berkala.
8. Jangan publikasikan URL admin sembarangan.
9. Update dependency secara berkala dan terjadwal.

## Catatan implementasi step ini

- Route admin diproteksi middleware `['auth', 'admin', 'security.headers']`.
- Middleware `admin` mengembalikan `403` jika user bukan admin.
- Security headers yang dipasang:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: camera=(), microphone=(), geolocation=()`
  - `X-XSS-Protection: 0`
