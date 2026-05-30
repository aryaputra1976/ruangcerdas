<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'template-kantor')->first();

        Product::updateOrCreate(
            ['slug' => 'kantor-cerdas-ai-kit'],
            [
                'category_id' => $category?->id,
                'name' => 'Kantor Cerdas AI Kit',
                'short_description' => 'Paket template, prompt AI, dan panduan digital untuk membantu pekerjaan administrasi kantor lebih cepat dan profesional.',
                'description' => 'Kantor Cerdas AI Kit adalah produk digital pertama dari Ruang Cerdas yang dirancang untuk membantu pekerjaan administrasi, penyusunan dokumen, laporan, SOP, dan penggunaan AI dalam pekerjaan kantor sehari-hari.',
                'benefits' => implode("\n", [
                    'Mempercepat pembuatan dokumen kantor.',
                    'Membantu menyusun surat, laporan, dan SOP lebih rapi.',
                    'Menghemat waktu kerja administrasi.',
                    'Meningkatkan kualitas dokumen agar terlihat profesional.',
                    'Membantu pengguna memanfaatkan AI untuk pekerjaan kantor.',
                ]),
                'contents' => implode("\n", [
                    'Template Surat Dinas/Perkantoran Premium',
                    'Template Laporan Kantor',
                    'Template SOP Kantor',
                    'Template Excel Administrasi',
                    '50 Prompt AI Perkantoran',
                    'Panduan AI Administrasi Kantor',
                    'Materi penjualan/landing page',
                    'File ZIP final produk digital',
                ]),
                'normal_price' => 149000,
                'sale_price' => 49000,
                'first_buyer_price' => 39000,
                'first_buyer_quota' => 10,
                'cover_image' => null,
                'digital_file_path' => 'products/kantor-cerdas-ai-kit.zip',
                'download_filename' => 'Kantor-Cerdas-AI-Kit.zip',
                'is_featured' => true,
                'is_active' => true,
                'published_at' => now(),
            ]
        );
    }
}