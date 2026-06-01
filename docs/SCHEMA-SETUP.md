# Schema Setup Ruang Cerdas

Implementasi JSON-LD public Ruang Cerdas.

## Partial Schema
- `resources/views/public/partials/schema/organization.blade.php`
- `resources/views/public/partials/schema/product.blade.php`
- `resources/views/public/partials/schema/breadcrumb.blade.php`
- `resources/views/public/partials/schema/faq.blade.php`

## Lokasi Render
- Home: `Organization`
- Detail produk: `Product` + `BreadcrumbList`
- Detail produk (opsional): `FAQPage` hanya jika FAQ aktif tersedia

## Keamanan Data
- Jangan render `digital_file_path`
- Jangan render `download_token`
- Jangan render `invoice_number`
- Jangan render data pembeli (nama/email/WA)

## Catatan Data Product
- `sku` menggunakan format `RC-{id}`
- `priceCurrency` tetap `IDR`
- `seller` menggunakan organisasi `Ruang Cerdas`

