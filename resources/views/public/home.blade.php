@extends('layouts.public')

@section('title', $landing['seo_title'] ?? 'Ruang Cerdas - Produk Digital Siap Pakai')
@section('meta_description', $landing['seo_description'] ?? 'Marketplace produk digital seperti template, ebook, file ZIP, dan aplikasi siap pakai.')
@section('meta_keywords', $landing['seo_keywords'] ?? '')
@section('og_image', $landing['og_image_url'] ?? '')

@section('content')
@php
    $secondaryCtaUrl = $landing['secondary_cta_url'] ?? '#cara-beli';
    if (\Illuminate\Support\Str::startsWith($secondaryCtaUrl, '#')) {
        $secondaryCtaUrl = route('home') . $secondaryCtaUrl;
    }
@endphp

<section class="relative overflow-hidden bg-slate-50">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,#dbeafe,transparent_45%),radial-gradient(circle_at_bottom_right,#dcfce7,transparent_35%)]"></div>
    <div class="mx-auto grid max-w-7xl gap-8 px-6 pt-12 pb-16 md:gap-10 md:pt-14 md:pb-16 lg:grid-cols-2 lg:items-center">
        <div class="max-w-2xl">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">{{ $landing['hero_badge'] }}</p>
            <h1 class="mt-3 max-w-xl text-4xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                {{ $landing['hero_title'] }}
            </h1>
            <p class="mt-3 max-w-2xl text-lg leading-8 text-slate-600">
                {{ $landing['hero_subtitle'] }}
            </p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $landing['primary_cta_url'] }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-7 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                    {{ $landing['primary_cta_text'] }}
                </a>
                <a href="{{ $secondaryCtaUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-7 py-4 text-base font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                    {{ $landing['secondary_cta_text'] }}
                </a>
            </div>

            <div class="mt-6 grid gap-2 sm:grid-cols-2">
                @foreach ([
                    'Pembayaran manual dengan verifikasi admin',
                    'Link download dikirim ke email pembeli',
                    'Token download aman sesuai masa berlaku',
                    'Bisa cek status order kapan saja',
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
                <p class="text-xs uppercase tracking-widest text-blue-200">Preview Paket Digital</p>
                <h3 class="mt-2 text-2xl font-black">Template, Prompt AI, dan Aplikasi Siap Pakai</h3>
                <p class="mt-2 text-sm leading-6 text-slate-300">
                    Semua produk dirancang agar langsung bisa dipakai untuk kerja harian tanpa setup rumit.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach (['eBook PDF', 'Template', 'Prompt AI', 'File ZIP', 'Aplikasi'] as $assetLabel)
                        <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-slate-100">
                            {{ $assetLabel }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['title' => 'Administrasi', 'desc' => 'Format dokumen siap edit'],
                    ['title' => 'Bisnis UMKM', 'desc' => 'Template operasional harian'],
                    ['title' => 'Prompt AI', 'desc' => 'Prompt siap pakai untuk kerja'],
                    ['title' => 'Tool Praktis', 'desc' => 'Aplikasi ringan siap jalan'],
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
                    @include('components.public.product-card', ['product' => $product])
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
                ['3', 'Bayar dan upload bukti', 'Lakukan pembayaran manual lalu upload bukti bayar.'],
                ['4', 'Admin approve, link download dikirim email', 'Setelah diverifikasi, link download aman dikirim ke email.'],
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
                @endphp
                <div class="mt-4 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="https://wa.me/{{ $supportNumber }}" target="_blank" rel="noopener noreferrer" class="rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                        Hubungi WhatsApp Admin
                    </a>
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
@endsection
