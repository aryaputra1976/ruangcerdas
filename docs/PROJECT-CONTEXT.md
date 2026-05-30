# Ruang Cerdas - Project Context

Ruang Cerdas adalah aplikasi Laravel untuk menjual produk digital seperti template, ebook, file ZIP, dan aplikasi siap pakai.

## Stack

- Laravel 12
- PHP 8.2+
- Blade
- Tailwind CSS untuk halaman public
- Hando admin assets untuk halaman admin
- MySQL atau SQLite sesuai konfigurasi `.env`
- Manual payment pada tahap awal
- Secure download menggunakan token

## Flow utama

1. Pembeli membuka halaman produk.
2. Pembeli melakukan checkout.
3. Sistem membuat order dengan status `pending`.
4. Pembeli membayar secara manual.
5. Pembeli upload bukti pembayaran.
6. Status order berubah menjadi `payment_uploaded`.
7. Admin memeriksa bukti pembayaran.
8. Admin approve order.
9. Sistem mengubah order menjadi `paid` dan membuat `download_token`.
10. Pembeli mengunduh file produk melalui link download aman.

## Modul yang sudah ada

- Public home page
- Public product listing
- Product detail
- Checkout manual payment
- Thank you page
- Upload bukti pembayaran
- Admin dashboard
- Admin order list/detail
- Admin approve/reject order
- Product CRUD
- Category CRUD
- Secure download dasar
- Download log

## Aturan penting

- File produk digital harus berada di disk `private`, bukan di folder public.
- Jangan mengubah flow checkout yang sudah jalan tanpa alasan kuat.
- Jangan mengubah struktur route public/admin secara besar-besaran.
- Gunakan patch kecil dan mudah diuji.
- Setiap fitur baru harus tetap cocok dengan konsep jual produk digital sederhana.

## Route utama

Public:

- `/`
- `/produk`
- `/produk/{product:slug}`
- `/checkout/{product:slug}`
- `/order/{invoice}/thank-you`
- `/order/{invoice}/upload-payment`
- `/order/{invoice}/download/{token}`

Admin:

- `/admin`
- `/admin/orders`
- `/admin/products`
- `/admin/categories`

## Model utama

- `Product`
- `Category`
- `Order`
- `DownloadLog`
- `User`

## Status order

- `pending`
- `payment_uploaded`
- `paid`
- `rejected`
- `cancelled`
- `expired`

## Target pengembangan

Ruang Cerdas dikembangkan bertahap dari aplikasi jual produk digital sederhana menjadi sistem penjualan produk digital yang aman, rapi, dan mudah dikelola.