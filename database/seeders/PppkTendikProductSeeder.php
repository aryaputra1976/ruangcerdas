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
            'Paket Persiapan PPPK Tendik Sekolah Rakyat 2026',
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
                'name' => 'Paket Persiapan PPPK Tendik Sekolah Rakyat 2026',
                'short_description' => 'Paket digital untuk membantu pelamar PPPK Tendik Sekolah Rakyat 2026 menyiapkan dokumen, memahami jadwal, dan belajar lebih terarah.',
                'description' => 'Paket Persiapan PPPK Tendik Sekolah Rakyat 2026 berisi ringkasan pengumuman, checklist dokumen, template surat lamaran, template surat pernyataan 10 poin, panduan daftar SSCASN, jadwal seleksi, tips lolos administrasi, dan latihan soal PPPK. Paket ini bukan produk resmi pemerintah, melainkan materi bantu belajar dan persiapan dokumen dari Ruang Cerdas.',
                'contents' => implode("\n", [
                    'Ringkasan pengumuman',
                    'Checklist dokumen',
                    'Template surat lamaran',
                    'Template surat pernyataan 10 poin',
                    'Panduan daftar SSCASN',
                    'Jadwal seleksi',
                    'Tips lolos administrasi',
                    'Latihan soal PPPK',
                ]),
                'benefits' => implode("\n", [
                    'Membantu menyiapkan dokumen lebih rapi',
                    'Membantu memahami alur daftar SSCASN',
                    'Mengurangi risiko kelalaian administrasi',
                    'Membantu belajar lebih terarah sebelum seleksi',
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
