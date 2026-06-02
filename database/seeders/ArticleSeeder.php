<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Cara Mulai Belajar CPNS dari Nol',
                'slug' => 'cara-mulai-belajar-cpns-dari-nol',
                'excerpt' => 'Langkah awal yang realistis untuk memulai persiapan CPNS agar belajar lebih terarah.',
                'content' => "Mulai dari memahami alur seleksi, menyusun target mingguan, dan menyiapkan materi dasar terlebih dahulu.\n\nFokus pada konsistensi belajar harian dan evaluasi berkala.",
                'is_published' => true,
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Checklist Berkas CPNS/PPPK untuk Pemula',
                'slug' => 'checklist-berkas-cpns-pppk-untuk-pemula',
                'excerpt' => 'Daftar berkas penting yang perlu disiapkan agar proses pendaftaran lebih rapi.',
                'content' => "Siapkan dokumen identitas, ijazah, transkrip, dan berkas pendukung lainnya sejak awal.\n\nGunakan checklist agar tidak ada berkas yang terlewat.",
                'is_published' => true,
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Kesalahan Umum Saat Menyiapkan Dokumen PPPK',
                'slug' => 'kesalahan-umum-saat-menyiapkan-dokumen-pppk',
                'excerpt' => 'Hindari kesalahan administratif yang sering membuat proses pendaftaran terhambat.',
                'content' => "Periksa format file, kelengkapan data, dan konsistensi nama antar dokumen.\n\nLakukan pengecekan akhir sebelum unggah.",
                'is_published' => false,
                'published_at' => null,
            ],
            [
                'title' => 'Cara Membuat Jadwal Belajar 30 Hari',
                'slug' => 'cara-membuat-jadwal-belajar-30-hari',
                'excerpt' => 'Panduan menyusun jadwal belajar 30 hari agar target belajar lebih realistis.',
                'content' => "Bagi materi ke dalam modul kecil per hari dan sisipkan sesi latihan soal.\n\nPastikan ada waktu review di akhir pekan.",
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Cara Menggunakan AI untuk Membantu Pekerjaan Kantor',
                'slug' => 'cara-menggunakan-ai-untuk-membantu-pekerjaan-kantor',
                'excerpt' => 'Contoh penggunaan AI untuk merapikan draft dokumen dan mempercepat pekerjaan rutin kantor.',
                'content' => "AI dapat membantu membuat draft awal dokumen, merangkum catatan, dan mempercepat penyusunan laporan.\n\nTetap lakukan validasi manual sebelum digunakan.",
                'is_published' => false,
                'published_at' => null,
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'content' => $article['content'],
                    'cover_image' => null,
                    'is_published' => $article['is_published'],
                    'published_at' => $article['published_at'],
                    'seo_title' => $article['title'] . ' - Ruang Cerdas',
                    'seo_description' => $article['excerpt'],
                ]
            );
        }
    }
}
