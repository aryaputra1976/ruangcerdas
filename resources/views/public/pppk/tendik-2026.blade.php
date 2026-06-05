@extends('layouts.public')

@section('title', 'PPPK Tendik Sekolah Rakyat 2026 - Ruang Cerdas')
@section('meta_description', 'Landing page publik PPPK Tendik Sekolah Rakyat 2026 dengan ringkasan formasi, jadwal penting, checklist dokumen, dan paket persiapan.')
@section('canonical', route('public.pppk.tendik'))

@php
    $packageProductSlug = 'paket-persiapan-pppk-tendik-sekolah-rakyat-2026';
    $packageProductUrl = \Illuminate\Support\Facades\Route::has('products.show')
        ? route('products.show', $packageProductSlug)
        : url('/produk/' . $packageProductSlug);

    $formationRows = [
        ['label' => 'Wali Asuh', 'count' => '3.191'],
        ['label' => 'Wali Asrama', 'count' => '1.343'],
        ['label' => 'Operator Sekolah', 'count' => '182'],
        ['label' => 'Pengelola Keuangan', 'count' => '226'],
        ['label' => 'Administrasi Perkantoran', 'count' => '185'],
        ['label' => 'Total', 'count' => '5.127'],
    ];

    $scheduleRows = [
        ['label' => 'Pengumuman Seleksi', 'date' => '3-15 Juni 2026'],
        ['label' => 'Pendaftaran', 'date' => '8-14 Juni 2026'],
        ['label' => 'Seleksi Administrasi', 'date' => '8-16 Juni 2026'],
        ['label' => 'Pengumuman Administrasi', 'date' => '17 Juni 2026'],
        ['label' => 'CAT BKN', 'date' => '26 Juni-5 Juli 2026'],
        ['label' => 'Pengumuman Hasil CAT', 'date' => '9 Juli 2026'],
        ['label' => 'Seleksi Kompetensi Tambahan', 'date' => '13-19 Juli 2026'],
        ['label' => 'Pengisian DRH & Pemberkasan', 'date' => '28 Juli-11 Agustus 2026'],
    ];

    $documentChecklist = [
        'Pas foto formal latar merah',
        'Surat lamaran bermeterai/e-meterai',
        'Surat pernyataan 10 poin bermeterai/e-meterai',
        'Ijazah asli',
        'Transkrip nilai asli',
        'Sertifikat akreditasi prodi/jurusan jika diperlukan',
        'Sertifikat SDM Kesejahteraan Sosial jika memiliki',
    ];

    $packageItems = [
        'Panduan lengkap PPPK Tendik',
        'Konteks Sekolah Rakyat',
        'Proses seleksi PPPK Tendik',
        'Strategi seleksi kompetensi',
        'Studi kasus peserta',
        'Checklist dokumen',
        'Template surat lamaran',
        'Template surat pernyataan',
        'Latihan soal & pembahasan',
        'Form kontrol berkas',
    ];

    $faqItems = [
        [
            'question' => 'Apakah ini PPPK daerah?',
            'answer' => 'Bukan. Ini seleksi PPPK Tendik Sekolah Rakyat di lingkungan Kementerian Sosial RI.',
        ],
        [
            'question' => 'Apakah SLTA bisa mendaftar?',
            'answer' => 'Bisa untuk formasi Administrasi Perkantoran sesuai pengumuman.',
        ],
        [
            'question' => 'Apakah paket ini resmi dari pemerintah?',
            'answer' => 'Tidak. Paket Ruang Cerdas adalah materi bantu belajar dan persiapan dokumen.',
        ],
    ];
@endphp

@section('content')
<section class="relative overflow-hidden bg-slate-950 py-16 text-white md:py-20">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.28),_transparent_36%),radial-gradient(circle_at_bottom_right,_rgba(234,179,8,0.18),_transparent_28%)]"></div>

    <div class="relative mx-auto max-w-7xl px-6">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.2fr)_minmax(320px,420px)] lg:items-center">
            <div>
                <p class="inline-flex rounded-full border border-blue-200/20 bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-widest text-blue-100">
                    PPPK Tendik 2026
                </p>
                <h1 class="mt-5 text-4xl font-black tracking-tight text-white md:text-5xl">
                    PPPK Tendik Sekolah Rakyat 2026 Dibuka
                </h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-slate-200 md:text-lg">
                    Kementerian Sosial RI membuka 5.127 formasi PPPK Tenaga Kependidikan Sekolah Rakyat.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="#checklist" class="inline-flex items-center justify-center rounded-3xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                        Lihat Checklist Dokumen
                    </a>
                    <a href="#paket" class="inline-flex items-center justify-center rounded-3xl border border-white/20 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                        Lihat Paket Persiapan
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/10 p-6 shadow-sm backdrop-blur">
                <p class="text-sm font-bold uppercase tracking-widest text-yellow-300">Ringkasan Cepat</p>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-white px-5 py-4 text-slate-950">
                        <p class="text-sm font-semibold text-slate-500">Formasi</p>
                        <p class="mt-2 text-3xl font-black text-blue-700">5.127</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white px-5 py-4 text-slate-950">
                        <p class="text-sm font-semibold text-slate-500">Pendaftaran</p>
                        <p class="mt-2 text-lg font-black text-slate-950">8-14 Juni 2026</p>
                    </div>
                </div>
                <div class="mt-4 rounded-3xl border border-yellow-200/20 bg-yellow-300/10 px-5 py-4 text-sm leading-7 text-slate-100">
                    Fokus utama halaman ini: cek formasi, catat jadwal, siapkan dokumen, lalu lanjut ke paket persiapan.
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <div class="mb-6">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ringkasan Formasi</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Sebaran Formasi PPPK Tendik</h2>
                </div>
                <div class="overflow-hidden rounded-3xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Formasi</th>
                                <th scope="col" class="px-5 py-4 text-right font-bold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-slate-700">
                            @foreach ($formationRows as $row)
                                <tr class="{{ $row['label'] === 'Total' ? 'bg-blue-50 text-slate-950' : '' }}">
                                    <td class="px-5 py-4 font-semibold">{{ $row['label'] }}</td>
                                    <td class="px-5 py-4 text-right font-black">{{ $row['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-blue-100 bg-gradient-to-b from-blue-600 to-slate-900 p-6 text-white shadow-sm md:p-8">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-100">Aksi Cepat</p>
                <h2 class="mt-3 text-2xl font-black">Siapkan sebelum pendaftaran dibuka penuh</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-100">
                    <li class="rounded-3xl border border-white/10 bg-white/10 px-4 py-3">Cek kesesuaian formasi dengan kualifikasi.</li>
                    <li class="rounded-3xl border border-white/10 bg-white/10 px-4 py-3">Lengkapi dokumen yang perlu meterai sejak awal.</li>
                    <li class="rounded-3xl border border-white/10 bg-white/10 px-4 py-3">Simpan jadwal CAT dan pemberkasan agar tidak terlewat.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="mb-6">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Jadwal Penting</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Timeline Seleksi 2026</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($scheduleRows as $row)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4">
                        <p class="text-sm font-semibold text-slate-500">{{ $row['label'] }}</p>
                        <p class="mt-2 text-lg font-black text-slate-950">{{ $row['date'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section id="checklist" class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,380px)]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Checklist Dokumen</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Dokumen yang Perlu Disiapkan</h2>
                <div class="mt-6 grid gap-3">
                    @foreach ($documentChecklist as $item)
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold leading-7 text-slate-700">
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-yellow-200 bg-yellow-50 p-6 shadow-sm md:p-8">
                <p class="text-sm font-bold uppercase tracking-widest text-yellow-700">Catatan</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">Cek ulang format file</h2>
                <p class="mt-4 text-sm leading-7 text-slate-700">
                    Dokumen administrasi biasanya gagal di tahap awal karena file tidak jelas, format tidak sesuai, atau surat belum ditandatangani dan ditempel meterai dengan benar.
                </p>
                <a href="#paket" class="mt-6 inline-flex items-center justify-center rounded-3xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                    Lihat Paket Persiapan
                </a>
            </div>
        </div>
    </div>
</section>

<section id="paket" class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm md:p-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Paket Digital Edukasi</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Panduan Lengkap PPPK Tendik Sekolah Rakyat</h2>
                    <p class="mt-4 text-base leading-8 text-slate-600">
                        Paket digital untuk membantu pemula memahami PPPK Tendik Sekolah Rakyat dari awal, mulai dari pengenalan jabatan, proses seleksi, strategi belajar, studi kasus, checklist dokumen, template surat, hingga latihan soal dan pembahasan.
                    </p>
                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        @foreach ($packageItems as $item)
                            <div class="rounded-3xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold leading-7 text-slate-700">
                                {{ $item }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-blue-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Harga</p>
                    <p class="mt-3 text-4xl font-black text-slate-950">Rp29.000</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Harga promo launching untuk akses paket digital PPPK Tendik berisi panduan, template, checklist, studi kasus, dan latihan soal.
                    </p>
                    <a href="{{ $packageProductUrl }}" class="mt-6 inline-flex w-full items-center justify-center rounded-3xl bg-blue-600 px-6 py-4 text-sm font-bold text-white transition hover:bg-blue-700">
                        Beli eBook Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">FAQ Singkat</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Pertanyaan yang Sering Muncul</h2>
            <div class="mt-6 space-y-3">
                @foreach ($faqItems as $faq)
                    <details class="group rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4">
                        <summary class="flex cursor-pointer list-none items-start justify-between gap-4 font-bold text-slate-950">
                            <span>{{ $faq['question'] }}</span>
                            <span class="mt-1 text-slate-400 transition group-open:rotate-45">+</span>
                        </summary>
                        <p class="mt-3 border-t border-slate-200 pt-3 text-sm leading-7 text-slate-600">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
