<?php

namespace Database\Seeders;

use App\Models\TryoutPackage;
use Illuminate\Database\Seeder;

class TryoutPackageSeeder extends Seeder
{
    public function run(): void
    {
        $legacyFreePackage = TryoutPackage::query()->where('slug', 'tryout-gratis-25-soal')->first();

        if ($legacyFreePackage && ! TryoutPackage::query()->where('slug', 'tryout-gratis-cpns-pemula')->exists()) {
            $legacyFreePackage->update([
                'slug' => 'tryout-gratis-cpns-pemula',
                'title' => 'Tryout Gratis CPNS Pemula',
            ]);
        }

        $packages = [
            [
                'slug' => 'tryout-gratis-cpns-pemula',
                'title' => 'Tryout Gratis CPNS Pemula',
                'description' => 'Coba simulasi dasar sebelum membeli paket lengkap.',
                'price' => 0,
                'is_free' => true,
                'duration_minutes' => 30,
                'twk_count' => 7,
                'tiu_count' => 8,
                'tkp_count' => 10,
                'attempt_limit' => 1,
                'access_days' => 7,
                'has_explanation' => false,
                'is_active' => true,
            ],
            [
                'slug' => 'tryout-premium-mini',
                'title' => 'Tryout Premium Mini',
                'description' => 'Latihan lebih banyak dengan pembahasan singkat.',
                'price' => 15000,
                'is_free' => false,
                'duration_minutes' => 60,
                'twk_count' => 15,
                'tiu_count' => 15,
                'tkp_count' => 20,
                'attempt_limit' => 3,
                'access_days' => 7,
                'has_explanation' => true,
                'is_active' => true,
            ],
            [
                'slug' => 'tryout-skd-premium-lengkap',
                'title' => 'Tryout SKD Premium Lengkap',
                'description' => 'Simulasi SKD lebih serius dengan komposisi soal lengkap.',
                'price' => 39000,
                'is_free' => false,
                'duration_minutes' => 100,
                'twk_count' => 30,
                'tiu_count' => 35,
                'tkp_count' => 45,
                'attempt_limit' => 3,
                'access_days' => 14,
                'has_explanation' => true,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            TryoutPackage::query()->updateOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}
