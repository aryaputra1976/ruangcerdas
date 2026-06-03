<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;

class TryoutFreeQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $twkCategory = QuestionCategory::query()->firstOrCreate(
            ['slug' => 'twk-default'],
            [
                'name' => 'TWK Default',
                'section' => 'TWK',
                'description' => 'Kategori default untuk soal Tes Wawasan Kebangsaan.',
                'is_active' => true,
            ]
        );

        $tiuCategory = QuestionCategory::query()->firstOrCreate(
            ['slug' => 'tiu-default'],
            [
                'name' => 'TIU Default',
                'section' => 'TIU',
                'description' => 'Kategori default untuk soal Tes Intelegensi Umum.',
                'is_active' => true,
            ]
        );

        $tkpCategory = QuestionCategory::query()->firstOrCreate(
            ['slug' => 'tkp-default'],
            [
                'name' => 'TKP Default',
                'section' => 'TKP',
                'description' => 'Kategori default untuk soal Tes Karakteristik Pribadi.',
                'is_active' => true,
            ]
        );

        foreach ($this->twkQuestions() as $item) {
            $this->seedQuestion($twkCategory->id, 'TWK', $item);
        }

        foreach ($this->tiuQuestions() as $item) {
            $this->seedQuestion($tiuCategory->id, 'TIU', $item);
        }

        foreach ($this->tkpQuestions() as $item) {
            $this->seedQuestion($tkpCategory->id, 'TKP', $item);
        }
    }

    private function seedQuestion(int $categoryId, string $section, array $item): void
    {
        $question = Question::query()->updateOrCreate(
            [
                'question_category_id' => $categoryId,
                'section' => $section,
                'question_text' => $item['question_text'],
            ],
            [
                'explanation' => $item['explanation'],
                'difficulty' => $item['difficulty'] ?? 'medium',
                'is_active' => true,
            ]
        );

        $question->options()->delete();
        $question->options()->createMany($item['options']);
    }

    private function twkQuestions(): array
    {
        return [
            [
                'question_text' => 'Dasar negara Republik Indonesia adalah ...',
                'explanation' => 'Pancasila merupakan dasar negara Republik Indonesia sebagaimana termuat dalam Pembukaan UUD 1945.',
                'options' => $this->objectiveOptions('A', [
                    'A' => 'Pancasila',
                    'B' => 'UUD 1945',
                    'C' => 'Bhinneka Tunggal Ika',
                    'D' => 'NKRI',
                    'E' => 'Tap MPR',
                ]),
            ],
            [
                'question_text' => 'Semboyan Bhinneka Tunggal Ika memiliki makna ...',
                'explanation' => 'Makna Bhinneka Tunggal Ika adalah berbeda-beda tetapi tetap satu jua.',
                'options' => $this->objectiveOptions('C', [
                    'A' => 'Bersatu kita teguh bercerai kita runtuh',
                    'B' => 'Keadilan sosial bagi seluruh rakyat',
                    'C' => 'Berbeda-beda tetapi tetap satu jua',
                    'D' => 'Rakyat memegang kedaulatan penuh',
                    'E' => 'Persatuan dibangun melalui kekuatan militer',
                ]),
            ],
            [
                'question_text' => 'Lembaga yang berwenang mengubah dan menetapkan UUD 1945 adalah ...',
                'explanation' => 'Pasal 3 UUD 1945 menegaskan MPR berwenang mengubah dan menetapkan Undang-Undang Dasar.',
                'options' => $this->objectiveOptions('D', [
                    'A' => 'Presiden',
                    'B' => 'DPR',
                    'C' => 'Mahkamah Konstitusi',
                    'D' => 'MPR',
                    'E' => 'DPD',
                ]),
            ],
            [
                'question_text' => 'Nilai yang terkandung dalam sila kedua Pancasila adalah ...',
                'explanation' => 'Sila kedua menekankan nilai kemanusiaan yang adil dan beradab.',
                'options' => $this->objectiveOptions('B', [
                    'A' => 'Ketuhanan',
                    'B' => 'Kemanusiaan',
                    'C' => 'Persatuan',
                    'D' => 'Kerakyatan',
                    'E' => 'Kesejahteraan',
                ]),
            ],
            [
                'question_text' => 'Contoh perilaku yang mencerminkan bela negara di lingkungan sekolah adalah ...',
                'explanation' => 'Bela negara dapat diwujudkan melalui disiplin, taat aturan, dan menjaga nama baik bangsa.',
                'options' => $this->objectiveOptions('E', [
                    'A' => 'Mengutamakan kelompok sendiri',
                    'B' => 'Melanggar tata tertib saat tidak diawasi',
                    'C' => 'Membiarkan teman merusak fasilitas umum',
                    'D' => 'Menolak upacara bendera tanpa alasan',
                    'E' => 'Disiplin belajar dan menaati tata tertib',
                ]),
            ],
            [
                'question_text' => 'Wilayah Indonesia terbentang dari Sabang sampai Merauke. Hal ini menunjukkan karakter NKRI yang ...',
                'explanation' => 'Indonesia adalah negara kesatuan dengan wilayah yang luas namun tetap satu pemerintahan.',
                'options' => $this->objectiveOptions('A', [
                    'A' => 'Utuh dan bersatu',
                    'B' => 'Terpisah-pisah',
                    'C' => 'Berbentuk konfederasi',
                    'D' => 'Berpusat pada daerah',
                    'E' => 'Tidak memiliki batas',
                ]),
            ],
            [
                'question_text' => 'Salah satu tujuan nasional Indonesia yang tercantum dalam Pembukaan UUD 1945 adalah ...',
                'explanation' => 'Tujuan nasional di antaranya mencerdaskan kehidupan bangsa.',
                'options' => $this->objectiveOptions('C', [
                    'A' => 'Memperluas wilayah kekuasaan',
                    'B' => 'Mengutamakan kepentingan golongan',
                    'C' => 'Mencerdaskan kehidupan bangsa',
                    'D' => 'Membatasi kerja sama internasional',
                    'E' => 'Mengurangi peran rakyat',
                ]),
            ],
        ];
    }

    private function tiuQuestions(): array
    {
        return [
            [
                'question_text' => 'Jika 15 + 28 = ...',
                'explanation' => '15 ditambah 28 sama dengan 43.',
                'options' => $this->objectiveOptions('B', [
                    'A' => '41',
                    'B' => '43',
                    'C' => '45',
                    'D' => '47',
                    'E' => '48',
                ]),
            ],
            [
                'question_text' => 'Deret angka berikutnya: 2, 4, 8, 16, ...',
                'explanation' => 'Pola deret dikali 2, sehingga angka berikutnya adalah 32.',
                'options' => $this->objectiveOptions('D', [
                    'A' => '18',
                    'B' => '20',
                    'C' => '24',
                    'D' => '32',
                    'E' => '36',
                ]),
            ],
            [
                'question_text' => 'Semua guru adalah pendidik. Sebagian pendidik adalah penulis. Kesimpulan yang tepat adalah ...',
                'explanation' => 'Tidak dapat disimpulkan semua guru penulis, tetapi mungkin ada guru yang penulis.',
                'options' => $this->objectiveOptions('C', [
                    'A' => 'Semua penulis adalah guru',
                    'B' => 'Tidak ada guru yang penulis',
                    'C' => 'Sebagian guru mungkin penulis',
                    'D' => 'Semua pendidik adalah guru',
                    'E' => 'Semua guru pasti penulis',
                ]),
            ],
            [
                'question_text' => 'Anton lebih tinggi dari Budi. Budi lebih tinggi dari Candra. Siapa yang paling pendek?',
                'explanation' => 'Karena Anton > Budi > Candra, maka Candra yang paling pendek.',
                'options' => $this->objectiveOptions('E', [
                    'A' => 'Anton',
                    'B' => 'Budi',
                    'C' => 'Anton dan Budi',
                    'D' => 'Tidak dapat ditentukan',
                    'E' => 'Candra',
                ]),
            ],
            [
                'question_text' => 'Sinonim kata "cermat" adalah ...',
                'explanation' => 'Cermat bermakna teliti.',
                'options' => $this->objectiveOptions('A', [
                    'A' => 'Teliti',
                    'B' => 'Lambat',
                    'C' => 'Kasar',
                    'D' => 'Ragu',
                    'E' => 'Bimbang',
                ]),
            ],
            [
                'question_text' => 'Jika x = 6 dan y = 4, maka 2x + 3y = ...',
                'explanation' => '2(6) + 3(4) = 12 + 12 = 24.',
                'options' => $this->objectiveOptions('C', [
                    'A' => '18',
                    'B' => '20',
                    'C' => '24',
                    'D' => '26',
                    'E' => '30',
                ]),
            ],
            [
                'question_text' => 'Lawan kata "optimis" adalah ...',
                'explanation' => 'Lawan kata optimis adalah pesimis.',
                'options' => $this->objectiveOptions('B', [
                    'A' => 'Semangat',
                    'B' => 'Pesimis',
                    'C' => 'Aktif',
                    'D' => 'Percaya',
                    'E' => 'Yakin',
                ]),
            ],
            [
                'question_text' => 'Sebuah persegi memiliki sisi 9 cm. Kelilingnya adalah ...',
                'explanation' => 'Keliling persegi = 4 × sisi = 4 × 9 = 36 cm.',
                'options' => $this->objectiveOptions('D', [
                    'A' => '18 cm',
                    'B' => '27 cm',
                    'C' => '32 cm',
                    'D' => '36 cm',
                    'E' => '81 cm',
                ]),
            ],
        ];
    }

    private function tkpQuestions(): array
    {
        return [
            [
                'question_text' => 'Anda mendapat tugas mendadak dengan tenggat singkat, sementara pekerjaan lama belum selesai. Apa yang paling tepat Anda lakukan?',
                'explanation' => 'Langkah terbaik adalah memetakan prioritas dan berkoordinasi agar semua tugas terselesaikan realistis.',
                'options' => $this->tkpOptions([
                    'A' => ['Menolak tugas baru agar fokus pada tugas lama', 2],
                    'B' => ['Mengerjakan tugas baru tanpa memberi tahu atasan', 3],
                    'C' => ['Meminta rekan lain mengerjakan semuanya', 1],
                    'D' => ['Menyusun prioritas dan komunikasi dengan atasan', 5],
                    'E' => ['Menunda semua tugas sampai situasi tenang', 1],
                ]),
            ],
            [
                'question_text' => 'Saat bekerja tim, ada anggota yang kurang aktif. Sikap terbaik Anda adalah ...',
                'explanation' => 'Pendekatan kolaboratif dan suportif membantu tim tetap produktif.',
                'options' => $this->tkpOptions([
                    'A' => ['Membiarkannya karena itu bukan tanggung jawab saya', 1],
                    'B' => ['Menegur di depan anggota lain agar jera', 2],
                    'C' => ['Mengajak berdiskusi dan mencari hambatannya', 5],
                    'D' => ['Mengambil semua tugasnya tanpa bicara', 3],
                    'E' => ['Melaporkan langsung tanpa konfirmasi', 2],
                ]),
            ],
            [
                'question_text' => 'Anda melihat rekan kerja melakukan kesalahan kecil pada laporan. Tindakan terbaik adalah ...',
                'explanation' => 'Mengoreksi dengan cara baik dan cepat mencegah dampak lebih besar.',
                'options' => $this->tkpOptions([
                    'A' => ['Diam saja karena bukan tugas saya', 1],
                    'B' => ['Menyindirnya di grup kerja', 1],
                    'C' => ['Membantu memperbaiki dan memberi tahu dengan sopan', 5],
                    'D' => ['Menunggu atasan yang menegur', 2],
                    'E' => ['Menyimpan bukti kesalahannya untuk berjaga-jaga', 2],
                ]),
            ],
            [
                'question_text' => 'Ketika melayani masyarakat yang marah, Anda sebaiknya ...',
                'explanation' => 'Tenang, empatik, dan fokus solusi adalah respon pelayanan terbaik.',
                'options' => $this->tkpOptions([
                    'A' => ['Membalas dengan nada tegas agar tertib', 1],
                    'B' => ['Mendengarkan, tetap tenang, lalu menawarkan solusi', 5],
                    'C' => ['Meminta masyarakat keluar dulu', 2],
                    'D' => ['Menyuruhnya kembali esok hari', 2],
                    'E' => ['Mengabaikannya sampai tenang sendiri', 1],
                ]),
            ],
            [
                'question_text' => 'Anda diberi masukan keras oleh atasan terkait hasil kerja. Respons paling tepat adalah ...',
                'explanation' => 'Menerima masukan secara profesional lalu memperbaiki pekerjaan menunjukkan kematangan kerja.',
                'options' => $this->tkpOptions([
                    'A' => ['Tersinggung dan menjelaskan pembelaan panjang', 2],
                    'B' => ['Diam namun tidak memperbaiki', 1],
                    'C' => ['Menerima masukan dan meminta arahan perbaikan', 5],
                    'D' => ['Menyalahkan rekan kerja lain', 1],
                    'E' => ['Menghindari atasan untuk sementara', 2],
                ]),
            ],
            [
                'question_text' => 'Saat aturan baru diberlakukan, sebagian rekan menolak menyesuaikan diri. Anda akan ...',
                'explanation' => 'Beradaptasi sambil membantu rekan memahami perubahan adalah tindakan yang konstruktif.',
                'options' => $this->tkpOptions([
                    'A' => ['Ikut menolak agar kompak', 1],
                    'B' => ['Menjalankan aturan dan mengajak diskusi positif', 5],
                    'C' => ['Menunggu sampai semua setuju dulu', 2],
                    'D' => ['Menghindari tugas yang terkait aturan baru', 1],
                    'E' => ['Mengerjakan seperlunya saja', 2],
                ]),
            ],
            [
                'question_text' => 'Anda mengetahui informasi penting menjelang rapat, tetapi waktu persiapan mepet. Tindakan terbaik adalah ...',
                'explanation' => 'Informasi relevan harus segera diringkas dan disampaikan agar keputusan rapat tetap berkualitas.',
                'options' => $this->tkpOptions([
                    'A' => ['Menyimpannya sampai rapat selesai', 1],
                    'B' => ['Menyampaikan inti informasi secepatnya', 5],
                    'C' => ['Menunggu diminta baru bicara', 2],
                    'D' => ['Menganggap bukan hal penting', 1],
                    'E' => ['Meneruskannya tanpa dicek sama sekali', 3],
                ]),
            ],
            [
                'question_text' => 'Jika target kerja tim belum tercapai karena koordinasi lemah, Anda akan ...',
                'explanation' => 'Evaluasi koordinasi dan penyusunan langkah perbaikan adalah respon terbaik.',
                'options' => $this->tkpOptions([
                    'A' => ['Menyalahkan anggota yang paling lambat', 1],
                    'B' => ['Menyarankan evaluasi dan pembagian tugas lebih jelas', 5],
                    'C' => ['Membiarkan karena nanti juga selesai', 1],
                    'D' => ['Keluar dari tim agar tidak ikut disalahkan', 1],
                    'E' => ['Mengerjakan bagian sendiri saja', 2],
                ]),
            ],
            [
                'question_text' => 'Anda harus memilih antara hasil cepat atau hasil teliti. Sikap terbaik adalah ...',
                'explanation' => 'Keseimbangan antara ketepatan waktu dan ketelitian perlu dijaga dengan perencanaan yang baik.',
                'options' => $this->tkpOptions([
                    'A' => ['Selalu pilih cepat walau berisiko salah', 1],
                    'B' => ['Selalu pilih teliti walau terlambat', 2],
                    'C' => ['Menyesuaikan prioritas sambil menjaga kualitas', 5],
                    'D' => ['Menunggu instruksi detail terus-menerus', 2],
                    'E' => ['Menyerahkan pilihan ke rekan kerja', 2],
                ]),
            ],
            [
                'question_text' => 'Dalam kondisi pekerjaan monoton, agar tetap produktif Anda sebaiknya ...',
                'explanation' => 'Menjaga motivasi dan membuat target kerja kecil membantu konsistensi produktivitas.',
                'options' => $this->tkpOptions([
                    'A' => ['Bekerja asal selesai', 1],
                    'B' => ['Menunda sampai mood membaik', 1],
                    'C' => ['Membuat target kecil dan menjaga fokus kerja', 5],
                    'D' => ['Sering berhenti agar tidak bosan', 2],
                    'E' => ['Meminta tugas orang lain supaya lebih menarik', 2],
                ]),
            ],
        ];
    }

    private function objectiveOptions(string $correctLabel, array $options): array
    {
        $items = [];

        foreach ($options as $label => $text) {
            $isCorrect = $label === $correctLabel;

            $items[] = [
                'option_label' => $label,
                'option_text' => $text,
                'is_correct' => $isCorrect,
                'score' => $isCorrect ? 5 : 0,
            ];
        }

        return $items;
    }

    private function tkpOptions(array $options): array
    {
        $items = [];

        foreach ($options as $label => [$text, $score]) {
            $items[] = [
                'option_label' => $label,
                'option_text' => $text,
                'is_correct' => false,
                'score' => $score,
            ];
        }

        return $items;
    }
}
