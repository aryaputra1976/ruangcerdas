# Landing Conversion Notes

Optimasi landing public Ruang Cerdas untuk traffic Meta Ads (mobile-first).

## Fokus Perubahan
- Hero lebih jelas: headline + subheadline + CTA utama ke `/produk`.
- CTA WhatsApp ditampilkan jika nomor support tersedia.
- Benefit utama above-the-fold:
  - hemat waktu
  - file siap pakai
  - download aman
- Product card diperjelas:
  - cover, kategori, nama, deskripsi singkat
  - harga aktif + label promo
  - CTA detail, checkout, dan WhatsApp
- Section konversi:
  - Cocok untuk siapa
  - Cara Beli (alur lengkap)
  - Trust & keamanan
  - FAQ ringkas + link ke `/faq`
- Sticky CTA mobile aktif hanya di:
  - home
  - produk index
  - produk detail

## Tracking Event
- `HeroCtaClick`
- `ProductCardClick`
- `ViewContent`
- `Contact`

## Catatan Privasi
- Event tracking tidak mengirim PII pembeli.
- Tidak ada expose private file path atau token.

