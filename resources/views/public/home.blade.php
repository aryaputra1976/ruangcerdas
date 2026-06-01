@extends('layouts.public')

@section('title', $landing['seo_title'] ?? 'Ruang Cerdas - Produk Digital & Belajar Online')
@section('meta_description', $landing['seo_description'] ?? 'Ruang Cerdas menyediakan eBook, template, tools AI, aplikasi siap pakai, paket belajar digital, dan persiapan tryout online untuk kerja dan belajar lebih cerdas.')
@section('meta_keywords', $landing['seo_keywords'] ?? '')
@section('og_image', $landing['og_image_url'] ?? '')
@section('canonical', route('home'))
@section('og_type', 'website')
@section('schema_jsonld')
    @php
        $supportNumber = preg_replace('/\D+/', '', (string) ($landing['support_whatsapp'] ?? ''));
        if (str_starts_with($supportNumber, '0')) {
            $supportNumber = '62' . substr($supportNumber, 1);
        }
    @endphp
    @include('public.partials.schema.organization', ['supportNumber' => $supportNumber])
@endsection

@section('content')
<section class="relative overflow-hidden bg-slate-50">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,#dbeafe,transparent_45%),radial-gradient(circle_at_bottom_right,#dcfce7,transparent_35%)]"></div>
    <div class="mx-auto grid max-w-7xl gap-8 px-6 pt-12 pb-16 md:gap-10 md:pt-14 md:pb-16 lg:grid-cols-2 lg:items-center">
        <div class="max-w-2xl">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">RUANG CERDAS</p>
            <h1 class="mt-3 max-w-xl text-4xl font-extrabold leading-[1.12] tracking-tight text-slate-950 sm:text-4xl md:text-5xl xl:text-[3.25rem]">
                Produk Digital dan Belajar Online untuk Kerja Lebih Cerdas
            </h1>
            <p class="mt-3 max-w-2xl text-lg leading-8 text-slate-600">
                Temukan eBook, template, tools AI, aplikasi siap pakai, serta paket belajar dan tryout online untuk membantu pekerjaan, bisnis, dan persiapan karier secara lebih terarah.
            </p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('products.index') }}" onclick="window.rcTrack && window.rcTrack('HeroCtaClick', {source: 'home_hero_products'});" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-7 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                    Lihat Produk
                </a>
                <a href="{{ route('home') }}#tryout" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-7 py-4 text-base font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                    Lihat Tryout
                </a>
                @if (!empty($landing['support_whatsapp']))
                    @php
                        $heroWaNumber = preg_replace('/\D+/', '', (string) $landing['support_whatsapp']);
                        if (str_starts_with($heroWaNumber, '0')) {
                            $heroWaNumber = '62' . substr($heroWaNumber, 1);
                        }
                        $heroWaMessage = 'Halo Ruang Cerdas, saya ingin tanya produk digital yang paling cocok untuk kebutuhan saya.';
                    @endphp
                    @if ($heroWaNumber !== '')
                        <a href="https://wa.me/{{ $heroWaNumber }}?text={{ rawurlencode($heroWaMessage) }}" target="_blank" rel="noopener noreferrer" onclick="window.rcTrack && window.rcTrack('Contact', {source: 'home_hero_whatsapp'});" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-7 py-4 text-base font-bold text-white shadow-lg shadow-green-600/20 hover:bg-green-700">
                            Tanya via WhatsApp
                        </a>
                    @endif
                @endif
            </div>

            <div class="mt-6 grid gap-2 sm:grid-cols-2">
                @foreach ([
                    'Hemat waktu kerja dengan file digital siap pakai',
                    'Template dan tools langsung bisa dipakai',
                    'Download aman setelah verifikasi admin',
                    'Link file dikirim lewat email pembeli',
                ] as $trustLine)
                    <div class="flex items-start gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm">
                        <span class="mt-0.5 inline-flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        {{ $trustLine }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/60 md:p-6">
            <div class="rounded-2xl bg-slate-900 p-5 text-white md:p-6">
                <p class="text-xs uppercase tracking-widest text-blue-200">PREVIEW PLATFORM</p>
                <h3 class="mt-2 text-2xl font-black">Produk Digital, Tryout Online, dan Paket Belajar Siap Pakai</h3>
                <p class="mt-2 text-sm leading-6 text-slate-300">
                    Semua produk dirancang agar mudah digunakan, mulai dari kebutuhan kerja harian, produktivitas, hingga persiapan belajar secara mandiri.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach (['eBook PDF', 'Template', 'Prompt AI', 'Tryout Online', 'Kelas Digital'] as $assetLabel)
                        <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-slate-100">
                            {{ $assetLabel }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['title' => 'Paket Belajar', 'desc' => 'Materi belajar terarah'],
                    ['title' => 'Tryout Online', 'desc' => 'Latihan soal dan skor otomatis'],
                    ['title' => 'Template Kerja', 'desc' => 'Format dokumen siap edit'],
                    ['title' => 'Tools Praktis', 'desc' => 'Aplikasi ringan siap jalan'],
                ] as $mockup)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-semibold text-slate-500">
                            Paket
                        </div>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ $mockup['title'] }}</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">{{ $mockup['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section id="paket-belajar" class="scroll-mt-24 bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Paket Belajar</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Paket Belajar Digital</h2>
            <p class="mt-3 text-slate-600">Materi belajar praktis dalam bentuk eBook, template, dan file digital yang bisa dipelajari secara mandiri.</p>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @foreach ([
                ['title' => 'CPNS Starter Kit', 'desc' => 'Panduan awal untuk memulai persiapan CPNS dengan lebih rapi.'],
                ['title' => 'Jadwal Belajar 30 Hari', 'desc' => 'Template jadwal belajar agar persiapan lebih terarah.'],
                ['title' => 'Ringkasan Materi SKD', 'desc' => 'Ringkasan TWK, TIU, dan TKP untuk belajar cepat dan terstruktur.'],
            ] as $studyPack)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900">{{ $studyPack['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $studyPack['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="tryout" class="scroll-mt-24 bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">Coming Soon</span>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Tryout Online Segera Hadir</h2>
            <p class="mt-3 text-slate-600">Ruang Cerdas sedang menyiapkan fitur latihan soal dan tryout online berbayar dengan timer, skor otomatis, dan pembahasan.</p>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'Timer Ujian', 'desc' => 'Simulasi waktu pengerjaan yang lebih realistis.'],
                ['title' => 'Skor Otomatis', 'desc' => 'Nilai langsung muncul setelah tryout selesai.'],
                ['title' => 'Pembahasan Soal', 'desc' => 'Peserta dapat belajar dari hasil pengerjaan.'],
                ['title' => 'Riwayat Nilai', 'desc' => 'Pantau perkembangan hasil latihan dari waktu ke waktu.'],
            ] as $tryoutFeature)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-black text-slate-900">{{ $tryoutFeature['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $tryoutFeature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Cocok Untuk Siapa</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Dibuat untuk kebutuhan kerja nyata</h2>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                'ASN dan staf kantor yang butuh format kerja rapi',
                'Pelaku UMKM yang ingin materi digital siap jalan',
                'Kreator digital yang perlu template produksi cepat',
                'Admin sekolah atau kantor yang mengelola dokumen rutin',
                'Pemula yang ingin produk siap pakai tanpa ribet',
                'Pengguna AI yang butuh template dan prompt siap pakai',
            ] as $segment)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm font-semibold leading-6 text-slate-700">
                    {{ $segment }}
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-10">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <h3 class="text-base font-black text-slate-900">Disclaimer</h3>
            <p class="mt-2 text-sm leading-7 text-slate-600">
                Ruang Cerdas adalah platform belajar mandiri dan produk digital. Materi, soal, pembahasan, dan tryout yang tersedia bersifat edukatif, tidak menjamin kelulusan CPNS/PPPK, dan tidak berafiliasi dengan BKN, SSCASN, atau instansi pemerintah mana pun.
            </p>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Manfaat</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Kenapa Ruang Cerdas?</h2>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-5">
            @foreach ([
                ['title' => 'Tidak mulai dari nol', 'desc' => 'Anda langsung pakai struktur yang sudah jadi.'],
                ['title' => 'Format sudah rapi', 'desc' => 'Dokumen dan materi siap edit dengan standar profesional.'],
                ['title' => 'Hemat waktu kerja', 'desc' => 'Kurangi pekerjaan berulang dengan template siap pakai.'],
                ['title' => 'Pembelian aman', 'desc' => 'Transaksi diverifikasi admin sebelum akses file diberikan.'],
                ['title' => 'Siap digunakan', 'desc' => 'Setelah pembayaran valid, produk bisa langsung dipakai.'],
            ] as $benefit)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-black text-slate-900">{{ $benefit['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $benefit['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-7 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Produk Unggulan</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">{{ $landing['featured_section_title'] }}</h2>
                <p class="mt-3 max-w-2xl text-slate-600">{{ $landing['featured_section_subtitle'] }}</p>
            </div>

            <a href="{{ route('products.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">
                Lihat semua produk ->
            </a>
        </div>

        @if ($featuredProducts->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredProducts as $product)
                    @include('components.public.product-card', [
                        'product' => $product,
                        'supportWhatsapp' => $landing['support_whatsapp'] ?? null,
                        'whatsappCtaText' => $landing['whatsapp_cta_text'] ?? 'Tanya via WhatsApp',
                        'whatsappDefaultMessage' => $landing['whatsapp_default_message'] ?? null,
                    ])
                @endforeach
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                <h3 class="text-xl font-bold text-slate-900">Produk belum tersedia</h3>
                <p class="mt-2 text-slate-600">Produk Ruang Cerdas akan segera ditampilkan.</p>
            </div>
        @endif
    </div>
</section>

<section class="bg-slate-950 py-14 md:py-16 text-white">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-300">Mulai Sekarang</p>
        <h2 class="mt-3 text-3xl font-black md:text-4xl">Sudah tahu kebutuhanmu? Langsung pilih produknya.</h2>
        <p class="mx-auto mt-3 max-w-2xl text-slate-300">
            Katalog kami berisi produk digital siap pakai untuk mempercepat pekerjaan, bisnis, dan proses operasional harian.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('products.index') }}" class="rounded-2xl bg-blue-600 px-7 py-4 text-base font-bold text-white hover:bg-blue-700">
                Lihat Produk
            </a>
            <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl border border-slate-500 px-7 py-4 text-base font-bold text-white hover:border-blue-400 hover:text-blue-300">
                Cek Order
            </a>
        </div>
    </div>
</section>

<section id="cara-beli" class="scroll-mt-24 bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Cara Beli</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Pembelian mudah dengan pembayaran manual</h2>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['1', 'Pilih produk', 'Buka katalog dan pilih produk digital yang dibutuhkan.'],
                ['2', 'Isi checkout', 'Masukkan nama, email aktif, dan nomor WhatsApp.'],
                ['3', 'Bayar manual', 'Lakukan transfer sesuai instruksi pembayaran.'],
                ['4', 'Upload bukti bayar', 'Upload bukti pembayaran yang jelas di halaman order.'],
                ['5', 'Admin verifikasi', 'Admin akan memvalidasi pembayaran Anda.'],
                ['6', 'Link download ke email', 'Setelah approved, link download aman dikirim ke email pembeli.'],
            ] as $step)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-base font-bold text-white">{{ $step[0] }}</div>
                    <h3 class="mt-4 text-lg font-bold">{{ $step[1] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Trust</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Kenapa aman membeli di Ruang Cerdas?</h2>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'Verifikasi Admin', 'desc' => 'Pembayaran diverifikasi manual oleh admin sebelum akses diberikan.'],
                ['title' => 'Token Aman', 'desc' => 'Download memakai token aman sesuai masa berlaku sistem.'],
                ['title' => 'Kirim ke Email', 'desc' => 'Link download dikirim ke email pembeli setelah pembayaran disetujui.'],
                ['title' => 'Bantuan Admin', 'desc' => 'Jika link bermasalah, tim support siap membantu pengecekan order.'],
            ] as $trust)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                            <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $trust['title'] }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $trust['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if (($testimonials ?? collect())->isNotEmpty())
<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Testimonial</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Dipercaya oleh pengguna Ruang Cerdas</h2>
        </div>

        <div class="mt-8 grid gap-4 md:gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($testimonials as $testimonial)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm md:p-6">
                    <div class="flex items-center gap-1 text-amber-500" aria-label="Rating {{ (int) $testimonial->rating }} dari 5">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 {{ $i <= (int) $testimonial->rating ? 'opacity-100' : 'opacity-20' }}" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.951-.69l1.069-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $testimonial->content }}</p>
                    <div class="mt-3">
                        <p class="font-bold text-slate-900">{{ $testimonial->name }}</p>
                        @if ($testimonial->role)
                            <p class="text-xs text-slate-500">{{ $testimonial->role }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section id="faq" class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">FAQ</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Pertanyaan yang sering ditanyakan</h2>
            <a href="{{ route('public.faq') }}" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:text-blue-700">
                Lihat FAQ lengkap ->
            </a>
        </div>

        <div class="mx-auto mt-8 grid max-w-5xl gap-4">
            @foreach ([
                ['q' => 'Bagaimana cara mendapatkan file?', 'a' => 'Setelah pembayaran disetujui admin, link download dikirim ke email pembeli.'],
                ['q' => 'Apakah pembayaran otomatis?', 'a' => 'Belum. Saat ini pembayaran dilakukan manual lalu upload bukti bayar.'],
                ['q' => 'Kapan link download dikirim?', 'a' => 'Link dikirim setelah proses verifikasi pembayaran selesai dan order berstatus paid.'],
                ['q' => 'Bagaimana jika link download bermasalah?', 'a' => 'Hubungi tim support Ruang Cerdas agar kami bantu pengecekan order dan akses download.'],
            ] as $faq)
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="font-bold text-slate-900">{{ $faq['q'] }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $faq['a'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Bantuan</p>
            <h3 class="mt-3 text-3xl font-black text-slate-900 md:text-4xl">{{ $landing['support_title'] }}</h3>
            <p class="mx-auto mt-2 max-w-2xl text-slate-600">{{ $landing['support_text'] }}</p>
            @if (!empty($landing['support_whatsapp']))
                @php
                    $supportNumber = preg_replace('/\D+/', '', (string) $landing['support_whatsapp']);
                    if (str_starts_with($supportNumber, '0')) {
                        $supportNumber = '62' . substr($supportNumber, 1);
                    }
                    $ctaText = trim((string) ($landing['whatsapp_cta_text'] ?? '')) ?: 'Hubungi WhatsApp Admin';
                    $defaultMessage = trim((string) ($landing['whatsapp_default_message'] ?? ''));
                    $waMessage = $defaultMessage !== ''
                        ? $defaultMessage
                        : 'Halo Ruang Cerdas, saya tertarik dengan produk di website Ruang Cerdas. Link: ' . route('home');
                    $waUrl = $supportNumber !== '' ? 'https://wa.me/' . $supportNumber . '?text=' . rawurlencode($waMessage) : null;
                @endphp
                <div class="mt-4 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    @if ($waUrl)
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" onclick="window.rcTrack && window.rcTrack('Contact', {source: 'home_support'});" class="rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                            {{ $ctaText }}
                        </a>
                    @endif
                    <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                        Cek Order
                    </a>
                    <a href="{{ route('products.index') }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                        Lihat Produk
                    </a>
                </div>
            @else
                <p class="mt-3 text-sm font-semibold text-slate-700">Hubungi admin melalui kontak yang tersedia.</p>
            @endif
            <p class="mt-5 text-sm text-slate-500">{{ $landing['footer_short_text'] }}</p>
        </div>
    </div>
</section>

<section class="bg-slate-950 py-14 md:py-16 text-white">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-300">Penutup</p>
        <h2 class="mt-3 text-3xl font-black md:text-4xl">Siap mulai lebih cepat?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-slate-300">
            Pilih produk digital Ruang Cerdas yang dibutuhkan, lanjutkan checkout, dan selesaikan pembelian dengan alur yang jelas.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('products.index') }}" class="rounded-2xl bg-blue-600 px-7 py-4 text-base font-bold text-white hover:bg-blue-700">
                Lihat Produk
            </a>
            <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl border border-slate-600 px-7 py-4 text-base font-bold text-white hover:border-blue-500 hover:text-blue-300">
                Cek Order
            </a>
        </div>
    </div>
</section>

<div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 p-3 backdrop-blur md:hidden">
    <div class="mx-auto flex max-w-7xl gap-2">
        <a href="{{ route('products.index') }}" onclick="window.rcTrack && window.rcTrack('HeroCtaClick', {source: 'sticky_home_products'});" class="inline-flex flex-1 items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white">
            Lihat Produk
        </a>
        @if (!empty($landing['support_whatsapp']))
            @php
                $stickyWaNumber = preg_replace('/\D+/', '', (string) $landing['support_whatsapp']);
                if (str_starts_with($stickyWaNumber, '0')) {
                    $stickyWaNumber = '62' . substr($stickyWaNumber, 1);
                }
                $stickyWaMessage = 'Halo Ruang Cerdas, saya ingin konsultasi produk digital.';
            @endphp
            @if ($stickyWaNumber !== '')
                <a href="https://wa.me/{{ $stickyWaNumber }}?text={{ rawurlencode($stickyWaMessage) }}" target="_blank" rel="noopener noreferrer" onclick="window.rcTrack && window.rcTrack('Contact', {source: 'sticky_home_whatsapp'});" class="inline-flex flex-1 items-center justify-center rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white">
                    WhatsApp
                </a>
            @endif
        @endif
    </div>
</div>
@endsection
