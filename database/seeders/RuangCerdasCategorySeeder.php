<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class RuangCerdasCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'CPNS & PPPK',
                'slug' => 'cpns-pppk',
                'description' => 'Produk digital untuk persiapan belajar dan administrasi seleksi CPNS/PPPK.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Administrasi Kerja',
                'slug' => 'administrasi-kerja',
                'description' => 'Template dan panduan praktis untuk kebutuhan administrasi kerja harian.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Skill Digital Pemula',
                'slug' => 'skill-digital-pemula',
                'description' => 'Materi dan panduan dasar untuk membangun skill digital dari level pemula.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Template Produktivitas',
                'slug' => 'template-produktivitas',
                'description' => 'Kumpulan template siap pakai untuk membantu kerja lebih rapi dan efisien.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Aplikasi Siap Pakai',
                'slug' => 'aplikasi-siap-pakai',
                'description' => 'Aplikasi praktis siap pakai untuk mendukung pekerjaan dan proses belajar.',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
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
