# Production Readiness Checklist

Checklist praktis sebelum deployment atau real sales testing Ruang Cerdas.

## 1. Environment Production
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` sudah memakai domain produksi
- [ ] Koneksi database produksi terverifikasi
- [ ] Konfigurasi mail (SMTP/driver) terverifikasi
- [ ] Konfigurasi queue/session/cache dicek (jika dipakai)
- [ ] Konfigurasi filesystem/storage sesuai produksi

## 2. Laravel Commands
- [ ] Jalankan `composer install --no-dev --optimize-autoloader`
- [ ] Jalankan `php artisan migrate --force`
- [ ] Jalankan `php artisan storage:link`
- [ ] Jalankan `php artisan config:cache`
- [ ] Jalankan `php artisan route:cache`
- [ ] Jalankan `php artisan view:cache`
- [ ] Jalankan `npm run build`

## 3. Payment Settings
- [ ] Minimal satu rekening bank aktif sudah terisi
- [ ] Rekening utama (primary) sudah dipilih
- [ ] QRIS sudah diupload (jika digunakan)
- [ ] Catatan pembayaran (`payment_note`) sudah dicek
- [ ] Tidak ada data dummy pembayaran
- [ ] Halaman thank-you pembayaran publik sudah dites

## 4. Product Readiness
- [ ] Produk berstatus aktif
- [ ] Produk sudah publish
- [ ] Produk visible ke publik
- [ ] Produk punya file digital private
- [ ] Cover image sudah diupload
- [ ] Harga produk sudah benar
- [ ] FAQ produk sudah dicek (jika dipakai)
- [ ] Preview produk dari admin sudah dicek
- [ ] Halaman detail produk publik sudah dicek

## 5. Order Flow Testing
- [ ] Buat test order end-to-end
- [ ] Checkout berjalan normal
- [ ] Halaman invoice/thank-you tampil normal
- [ ] Instruksi pembayaran tampil benar
- [ ] Upload bukti pembayaran berjalan
- [ ] Admin bisa lihat bukti via secure admin route
- [ ] Approve order berjalan
- [ ] Email link download terkirim
- [ ] Link download bisa dipakai
- [ ] Expiry dan batas download terverifikasi
- [ ] Order tracking berjalan

## 6. Admin Readiness
- [ ] Login admin berjalan
- [ ] Filter pencarian order list berjalan
- [ ] Halaman detail order berjalan
- [ ] Safety approve/reject sudah dicek
- [ ] Resend link download sudah dicek
- [ ] Catatan internal order berjalan
- [ ] Audit trail order tampil benar
- [ ] Payment settings admin sudah dicek
- [ ] Reports/analytics dicek (jika tersedia)

## 7. Public SEO Readiness
- [ ] Title/meta homepage sudah dicek
- [ ] Title/meta detail produk sudah dicek
- [ ] `sitemap.xml` bisa diakses
- [ ] `robots.txt` bisa diakses
- [ ] Halaman order/checkout/tracking noindex
- [ ] Canonical URL sudah dicek
- [ ] Favicon/logo tampil benar

## 8. Security Safety
- [ ] `APP_DEBUG=false`
- [ ] Tidak ada raw download token di UI
- [ ] Tidak ada private file path di UI
- [ ] Bukti pembayaran admin tidak lewat public asset link
- [ ] Semua route admin terlindungi middleware
- [ ] File private produk tidak bisa diakses publik langsung
- [ ] Validasi upload file sudah dicek
- [ ] File `.env` tidak terekspos publik

## 9. Backup & Recovery
- [ ] Backup database sebelum deploy
- [ ] Backup file storage publik
- [ ] Backup file private produk
- [ ] Kebijakan backup QRIS dan bukti pembayaran jelas
- [ ] Rencana rollback sudah disiapkan

## 10. Final Go Live Test
- [ ] Uji alur di desktop
- [ ] Uji alur di mobile
- [ ] Uji email pembeli
- [ ] Uji WhatsApp/kontak support
- [ ] Uji pembelian dari homepage sampai download
- [ ] Tandai status akhir: Ready / Not Ready

## Final Acceptance
Ruang Cerdas siap produksi jika semua poin kritis sudah dicentang.
