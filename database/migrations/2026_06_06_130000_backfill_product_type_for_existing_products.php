<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            'cpns-pppk-starter-kit' => [
                'product_type' => 'ebook',
                'category' => 'cpns',
            ],
            'tryout-premium-mini' => [
                'product_type' => 'tryout',
                'category' => 'cpns',
            ],
            'tryout-skd-premium-lengkap' => [
                'product_type' => 'tryout',
                'category' => 'cpns',
            ],
            'paket-persiapan-pppk-tendik-sekolah-rakyat-2026' => [
                'product_type' => 'ebook',
                'category' => 'pppk-tendik',
            ],
        ];

        foreach ($updates as $slug => $attributes) {
            DB::table('products')
                ->where('slug', $slug)
                ->update($attributes);
        }
    }

    public function down(): void
    {
        DB::table('products')
            ->whereIn('slug', [
                'cpns-pppk-starter-kit',
                'tryout-premium-mini',
                'tryout-skd-premium-lengkap',
                'paket-persiapan-pppk-tendik-sekolah-rakyat-2026',
            ])
            ->update([
                'product_type' => null,
                'category' => null,
            ]);
    }
};
