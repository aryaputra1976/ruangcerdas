# Tracking Setup Ruang Cerdas

Panduan ringkas konfigurasi tracking untuk halaman public Ruang Cerdas.

## 1) Field Tracking di Admin
Masuk ke `Admin -> Landing Settings`, isi jika diperlukan:
- `meta_pixel_id`
- `google_analytics_id` (GA4, contoh format `G-XXXXXXX`)
- `google_tag_manager_id` (contoh format `GTM-XXXXXXX`)
- `whatsapp_cta_text`
- `whatsapp_default_message`

Semua field boleh kosong. Script tracking hanya akan dirender jika ID terisi.

## 2) Posisi Script
- Script head: `resources/views/public/partials/tracking-head.blade.php`
- Script body (noscript GTM): `resources/views/public/partials/tracking-body.blade.php`
- Layout include: `resources/views/layouts/public.blade.php`

## 3) Event Tracking yang Dipakai
- `ViewContent`: saat halaman detail produk dibuka
- `InitiateCheckout`: saat tombol checkout produk diklik
- `Lead`: saat form checkout disubmit
- `PurchasePending`: saat form upload bukti bayar disubmit
- `Contact`: saat CTA WhatsApp diklik

## 4) Privasi Data
- Jangan kirim PII ke tracker.
- Event tidak mengirim:
  - nama pembeli
  - email pembeli
  - nomor WhatsApp pembeli
  - invoice number

## 5) WhatsApp CTA
- Nomor WA dibersihkan ke digit saja.
- Jika awalan `0`, otomatis diubah menjadi awalan `62`.
- Format URL: `https://wa.me/{nomor}?text={pesan_ter-encode}`.

## 6) Verifikasi Setelah Deploy
1. Cek source halaman public, pastikan script sesuai ID yang diisi.
2. Uji klik tombol checkout dan CTA WhatsApp.
3. Uji submit checkout dan upload pembayaran.
4. Cek event masuk di dashboard Meta/GA/GTM.

