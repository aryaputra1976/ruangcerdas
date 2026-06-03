<?php

namespace Database\Seeders;

use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;

class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'twk-default',
                'name' => 'TWK Default',
                'section' => 'TWK',
                'description' => 'Kategori default untuk soal Tes Wawasan Kebangsaan.',
            ],
            [
                'slug' => 'tiu-default',
                'name' => 'TIU Default',
                'section' => 'TIU',
                'description' => 'Kategori default untuk soal Tes Intelegensi Umum.',
            ],
            [
                'slug' => 'tkp-default',
                'name' => 'TKP Default',
                'section' => 'TKP',
                'description' => 'Kategori default untuk soal Tes Karakteristik Pribadi.',
            ],
        ];

        foreach ($categories as $category) {
            QuestionCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'section' => $category['section'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
