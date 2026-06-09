<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TryoutPremiumProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'cpns-pppk'],
            [
                'name' => 'CPNS & PPPK',
                'description' => 'Produk digital untuk persiapan CPNS dan PPPK.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $products = [
            [
                'slug' => 'tryout-premium-mini',
                'name' => 'Tryout Premium Mini',
                'short_description' => 'Akses tryout premium mini dengan 50 soal, pembahasan singkat, dan 3 kali percobaan.',
                'description' => 'Produk akses untuk membuka Tryout Premium Mini RuangCerdas.',
                'normal_price' => 15000,
                'sale_price' => 15000,
                'contents' => "Akses Tryout Premium Mini\n50 soal\nPembahasan singkat\n3 kali percobaan",
                'benefits' => "Latihan lebih banyak\nBisa diakses selama masa aktif\nCocok untuk simulasi cepat",
                'digital_file_path' => 'products/tryout-premium-mini-access.txt',
                'download_filename' => 'akses-tryout-premium-mini.txt',
            ],
            [
                'slug' => 'tryout-skd-premium-lengkap',
                'name' => 'Tryout SKD Premium Lengkap',
                'short_description' => 'Akses tryout SKD lengkap 110 soal dengan pembahasan dan 3 kali percobaan.',
                'description' => 'Produk akses untuk membuka Tryout SKD Premium Lengkap RuangCerdas.',
                'normal_price' => 39000,
                'sale_price' => 39000,
                'contents' => "Akses Tryout SKD Premium Lengkap\n110 soal\nPembahasan\n3 kali percobaan",
                'benefits' => "Komposisi soal lengkap\nMasa akses lebih panjang\nCocok untuk simulasi serius",
                'digital_file_path' => 'products/tryout-skd-premium-lengkap-access.txt',
                'download_filename' => 'akses-tryout-skd-premium-lengkap.txt',
            ],
        ];

        foreach ($products as $product) {
            Storage::disk('private')->put(
                $product['digital_file_path'],
                "Produk ini membuka akses tryout premium RuangCerdas.\nSilakan kembali ke halaman tryout dan mulai paket yang sudah dibeli."
            );

            Product::query()->updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'category_id' => $category->id,
                    'product_type' => 'tryout',
                    'category' => 'cpns',
                    'name' => $product['name'],
                    'short_description' => $product['short_description'],
                    'description' => $product['description'],
                    'contents' => $product['contents'],
                    'benefits' => $product['benefits'],
                    'normal_price' => $product['normal_price'],
                    'sale_price' => $product['sale_price'],
                    'first_buyer_price' => null,
                    'first_buyer_quota' => null,
                    'cover_image' => null,
                    'digital_file_path' => $product['digital_file_path'],
                    'download_filename' => $product['download_filename'],
                    'file_size' => Storage::disk('private')->size($product['digital_file_path']),
                    'file_mime_type' => 'text/plain',
                    'file_uploaded_at' => now(),
                    'is_featured' => false,
                    'is_active' => true,
                    'published_at' => now(),
                ]
            );
        }
    }
}
