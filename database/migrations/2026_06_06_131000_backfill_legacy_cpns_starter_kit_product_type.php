<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where('slug', 'cpns-starter-kit-panduan-persiapan-cpns-untuk-pemula')
            ->update([
                'product_type' => 'ebook',
                'category' => 'cpns',
            ]);
    }

    public function down(): void
    {
        DB::table('products')
            ->where('slug', 'cpns-starter-kit-panduan-persiapan-cpns-untuk-pemula')
            ->update([
                'product_type' => null,
                'category' => null,
            ]);
    }
};
