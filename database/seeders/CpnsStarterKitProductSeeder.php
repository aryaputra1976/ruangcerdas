<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CpnsStarterKitProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::updateOrCreate(
            ['slug' => 'cpns-pppk'],
            [
                'name' => 'CPNS & PPPK',
                'description' => 'Produk digital untuk persiapan belajar dan administrasi seleksi CPNS/PPPK.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'cpns-pppk-starter-kit'],
            [
                'category_id' => $category->id,
                'product_type' => 'ebook',
                'category' => 'cpns',
                'name' => 'CPNS/PPPK Starter Kit',
                'short_description' => 'Panduan praktis untuk pemula agar lebih siap memahami alur seleksi, menyusun dokumen, dan belajar dengan jadwal yang lebih terarah.',
                'description' => 'CPNS/PPPK Starter Kit dirancang untuk membantu pemula memulai persiapan seleksi secara lebih terstruktur, mulai dari memahami alur pendaftaran, menyiapkan dokumen, hingga membangun ritme belajar harian.',
                'contents' => implode("\n", [
                    'eBook Panduan CPNS/PPPK untuk Pemula',
                    'Checklist Dokumen Pendaftaran',
                    'Jadwal Belajar 30 Hari',
                    'Ringkasan Dasar TWK, TIU, dan TKP',
                    'Template Catatan Belajar',
                    'Mini Simulasi Soal',
                    'Panduan Memilih Formasi',
                    'Daftar Link Resmi Penting',
                ]),
                'benefits' => implode("\n", [
                    'Membantu pemula mulai belajar dari nol',
                    'Membantu menyusun persiapan lebih rapi',
                    'Mengurangi kebingungan saat menyiapkan dokumen',
                    'Memberi struktur belajar harian',
                    'Bisa digunakan untuk belajar mandiri dari rumah',
                ]),
                'normal_price' => 99000,
                'sale_price' => 49000,
                'first_buyer_price' => 39000,
                'first_buyer_quota' => 10,
                'cover_image' => null,
                // Produk harus diberi file digital private terlebih dahulu sebelum diaktifkan dan dipublish.
                'digital_file_path' => null,
                'download_filename' => null,
                'is_featured' => true,
                'is_active' => false,
                'published_at' => null,
            ]
        );
    }
}
