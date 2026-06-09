<?php

namespace Database\Seeders;

use App\Models\QuestionCategory;
use App\Support\TryoutBlueprint;
use Illuminate\Database\Seeder;

class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'twk-default',
                'name' => 'TWK Default',
                'tryout_type' => TryoutBlueprint::TYPE_CPNS,
                'section' => 'twk',
                'description' => 'Kategori default untuk soal Tes Wawasan Kebangsaan.',
            ],
            [
                'slug' => 'tiu-default',
                'name' => 'TIU Default',
                'tryout_type' => TryoutBlueprint::TYPE_CPNS,
                'section' => 'tiu',
                'description' => 'Kategori default untuk soal Tes Intelegensi Umum.',
            ],
            [
                'slug' => 'tkp-default',
                'name' => 'TKP Default',
                'tryout_type' => TryoutBlueprint::TYPE_CPNS,
                'section' => 'tkp',
                'description' => 'Kategori default untuk soal Tes Karakteristik Pribadi.',
            ],
        ];

        foreach ($categories as $category) {
            QuestionCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'tryout_type' => $category['tryout_type'],
                    'section' => $category['section'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
