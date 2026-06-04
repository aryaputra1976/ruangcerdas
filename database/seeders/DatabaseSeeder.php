<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            RuangCerdasCategorySeeder::class,
            ProductSeeder::class,
            CpnsStarterKitProductSeeder::class,
            PppkTendikProductSeeder::class,
            TryoutPremiumProductSeeder::class,
            ArticleSeeder::class,
            TryoutPackageSeeder::class,
            QuestionCategorySeeder::class,
            TryoutFreeQuestionSeeder::class,
        ]);
    }
}
