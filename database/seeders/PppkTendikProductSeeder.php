<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PppkTendikProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()->updateOrCreate(
            ['slug' => 'cpns-pppk'],
            [
                'name' => 'CPNS & PPPK',
                'description' => 'Produk digital untuk persiapan belajar dan administrasi seleksi CPNS/PPPK.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $slug = 'paket-persiapan-pppk-tendik-sekolah-rakyat-2026';
        $digitalFilePath = 'products/' . $slug . '.txt';
        $downloadFilename = $slug . '.txt';
        $placeholderContent = implode("\n", [
            'Panduan Lengkap PPPK Tendik Sekolah Rakyat',
            '',
            'File placeholder ini dibuat agar produk dapat tampil di halaman publik Ruang Cerdas.',
            'Materi final dapat menggantikan file ini kapan saja melalui admin produk.',
            '',
            'Produk ini bukan dokumen resmi pemerintah.',
        ]);

        Storage::disk('private')->put($digitalFilePath, $placeholderContent);

        Product::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'category_id' => $category->id,
                'product_type' => 'ebook',
                'category' => 'pppk-tendik',
                'name' => 'Panduan Lengkap PPPK Tendik Sekolah Rakyat',
                'short_description' => 'Paket digital untuk membantu pemula memahami PPPK Tendik Sekolah Rakyat dari awal hingga siap belajar dan menyiapkan berkas.',
                'description' => 'Paket digital untuk membantu pemula memahami PPPK Tendik Sekolah Rakyat dari awal, mulai dari pengenalan jabatan, proses seleksi, strategi belajar, studi kasus, checklist dokumen, template surat, hingga latihan soal dan pembahasan. Paket ini bukan produk resmi pemerintah, melainkan materi bantu belajar dan persiapan dari Ruang Cerdas.',
                'contents' => implode("\n", [
                    'Panduan lengkap PPPK Tendik',
                    'Konteks Sekolah Rakyat',
                    'Proses seleksi PPPK Tendik',
                    'Strategi seleksi kompetensi',
                    'Studi kasus peserta',
                    'Checklist dokumen',
                    'Template surat lamaran',
                    'Template surat pernyataan',
                    'Latihan soal & pembahasan',
                    'Form kontrol berkas',
                ]),
                'benefits' => implode("\n", [
                    'Membantu pemula memahami alur PPPK Tendik dari awal',
                    'Membantu menyiapkan berkas dan template lebih rapi',
                    'Membantu belajar lebih terarah lewat studi kasus dan latihan soal',
                    'Membantu mengontrol kelengkapan dokumen sebelum mendaftar',
                ]),
                'normal_price' => 29000,
                'sale_price' => 29000,
                'first_buyer_price' => null,
                'first_buyer_quota' => null,
                'cover_image' => null,
                'digital_file_path' => $digitalFilePath,
                'download_filename' => $downloadFilename,
                'file_size' => Storage::disk('private')->size($digitalFilePath),
                'file_mime_type' => 'text/plain',
                'file_uploaded_at' => now(),
                'is_featured' => true,
                'is_active' => true,
                'published_at' => now(),
            ]
        );
    }
}
