<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Template Kantor',
                'description' => 'Template administrasi, surat, laporan, SOP, dan kebutuhan perkantoran.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Template Bisnis',
                'description' => 'Template untuk UMKM, proposal, penawaran, invoice, dan kebutuhan bisnis.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Template Excel',
                'description' => 'Template Excel siap pakai untuk administrasi, keuangan, data, dan laporan.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Prompt AI',
                'description' => 'Kumpulan prompt AI untuk mempercepat pekerjaan dan produktivitas.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Aplikasi Siap Pakai',
                'description' => 'Aplikasi sederhana siap pakai untuk kebutuhan kerja dan bisnis.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Source Code',
                'description' => 'Source code aplikasi untuk pembelajaran, pengembangan, dan implementasi.',
                'sort_order' => 6,
            ],
            [
                'name' => 'Panduan Digital',
                'description' => 'Ebook, panduan, tutorial, dan materi digital praktis.',
                'sort_order' => 7,
            ],
            [
                'name' => 'Layanan Custom',
                'description' => 'Layanan pembuatan template, aplikasi, otomasi, dan tools sesuai kebutuhan.',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}