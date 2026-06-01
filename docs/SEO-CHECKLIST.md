# SEO Checklist Ruang Cerdas

Checklist teknis dasar sebelum submit ke Google Search Console.

## Konfigurasi Dasar
- [ ] `APP_URL` production di-set ke `https://ruangcerdas.id`
- [ ] SSL aktif di hosting
- [ ] Redirect HTTP ke HTTPS aktif di level hosting/server
- [ ] Domain final konsisten `https://ruangcerdas.id` (hindari chain redirect)
- [ ] Jika `www` aktif, arahkan ke non-`www` (atau sebaliknya) secara konsisten
- [ ] Pastikan tidak ada mixed content (asset/script/image non-HTTPS)

## Robots & Sitemap
- [ ] `https://ruangcerdas.id/robots.txt` bisa diakses
- [ ] `robots.txt` memuat `Sitemap: https://ruangcerdas.id/sitemap.xml`
- [ ] `https://ruangcerdas.id/sitemap.xml` bisa diakses dan valid XML
- [ ] Sitemap sudah disubmit ke Google Search Console

## Indexing
- [ ] Halaman publik utama tidak `noindex` (`/`, `/produk`, `/produk/{slug}`, `/faq`, `/terms`, `/privacy`)
- [ ] Halaman sensitif `noindex,nofollow` (checkout, order, upload payment, tracking result)
- [ ] Coverage indexing dipantau di Search Console

## Canonical
- [ ] Home canonical ke `/`
- [ ] Produk index canonical ke `/produk`
- [ ] Produk detail canonical ke `/produk/{slug}`
- [ ] FAQ/Terms/Privacy canonical ke URL masing-masing

## Kualitas Teknis
- [ ] Tidak ada 404 penting (monitor Search Console + server logs)
- [ ] Mobile friendly lulus pengecekan dasar
- [ ] Performa dasar aman (gambar lazy-load, aset Vite ter-load normal)
- [ ] Cover produk dioptimalkan (WebP/JPG terkompres, ukuran wajar)

## Perintah Optimasi Laravel Production
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan optimize`
- [ ] `php artisan optimize:clear` (saat rollback/debug)

## Catatan Lanjutan
- [ ] Schema markup akan dicek lebih detail di Step 49B
