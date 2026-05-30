# Codex Rules - Ruang Cerdas

Gunakan aturan ini saat mengerjakan repository Ruang Cerdas dengan Codex.

## Prinsip kerja

- Kerjakan satu step kecil per perubahan.
- Jangan refactor besar tanpa diminta.
- Jangan mengubah flow checkout, order, payment, dan download jika tidak diperlukan.
- Jangan menghapus Hando admin layout.
- Jangan mencampur layout public dan admin.
- Jangan menaruh file produk digital di public.
- Selalu prioritaskan keamanan download file digital.

## Sebelum mengubah kode

Baca file berikut:

1. `docs/PROJECT-CONTEXT.md`
2. `docs/ROADMAP.md`
3. `routes/public.php`
4. `routes/admin.php`
5. Model terkait
6. Controller terkait
7. Service terkait

## Setelah mengubah kode

Jalankan:

```bash
php artisan config:clear
php artisan route:list
php artisan test
npm run build