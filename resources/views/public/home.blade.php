@extends('layouts.public')

@section('title', $landing['seo_title'] ?? 'Ruang Cerdas - Produk Digital Praktis untuk Belajar, Kerja, dan Seleksi ASN')
@section('meta_description', $landing['seo_description'] ?? 'Ruang Cerdas menyediakan eBook, checklist, template, dan panduan siap pakai untuk membantu Anda belajar lebih terarah, bekerja lebih rapi, dan menyiapkan administrasi dengan lebih mudah.')
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

        $pppkTendikUrl = \Illuminate\Support\Facades\Route::has('public.pppk.tendik')
            ? route('public.pppk.tendik')
            : url('/pppk-tendik-sekolah-rakyat-2026');

        $heroBadge = trim((string) ($landing['hero_badge'] ?? '')) ?: 'Ruang Cerdas';
        $heroTitle = trim((string) ($landing['hero_title'] ?? '')) ?: 'Mulai Persiapan CPNS & PPPK dengan Materi Digital yang Lebih Praktis';
        $heroSubtitle = trim((string) ($landing['hero_subtitle'] ?? '')) ?: 'Pilih starter kit, panduan, dan template digital yang membantu Anda belajar lebih terarah, menyiapkan administrasi lebih rapi, dan mulai tanpa bingung dari nol.';
        $heroHighlights = [
            'Fokus CPNS & PPPK',
            'File digital siap akses',
            'Panduan praktis untuk pemula',
        ];

        $recommendedNowProducts = $featuredProducts
            ->sortByDesc(function ($product) {
                $source = strtolower(trim(implode(' ', [
                    $product->name,
                    $product->slug,
                    $product->short_description,
                    $product->category?->name,
                    $product->category,
                ])));

                return str_contains($source, 'cpns') || str_contains($source, 'pppk') ? 1 : 0;
            })
            ->take(3)
            ->values();
    @endphp
    @include('public.partials.schema.organization', ['supportNumber' => $supportNumber])
@endsection

@section('content')
<section class="relative overflow-hidden bg-slate-50">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,#dbeafe,transparent_45%),radial-gradient(circle_at_bottom_right,#dcfce7,transparent_35%)]"></div>
    <div class="mx-auto grid max-w-7xl gap-8 px-6 py-10 md:py-12 lg:grid-cols-2 lg:items-center">
        <div class="max-w-2xl">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">{{ $heroBadge }}</p>
            <h1 class="mt-3 text-4xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-5xl">
                {{ $heroTitle }}
            </h1>
            <p class="mt-4 text-base leading-8 text-slate-600 md:text-lg">{{ $heroSubtitle }}</p>

            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($heroHighlights as $highlight)
                    <div class="rounded-full border border-blue-100 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                        {{ $highlight }}
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $landing['primary_cta_url'] }}" class="rc-btn-primary px-7 py-4 text-base">
                    {{ $landing['primary_cta_text'] }}
                </a>
                <a href="{{ $landing['secondary_cta_url'] }}" class="rc-btn-neutral px-7 py-4 text-base">
                    {{ $landing['secondary_cta_text'] }}
                </a>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Prioritas Saat Ini</p>
            <h2 class="mt-3 text-2xl font-black text-slate-950">Mulai dari produk CPNS & PPPK yang paling dicari</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    'Starter kit CPNS & PPPK untuk mulai tanpa bingung',
                    'Template administrasi pendukung belajar dan kerja',
                    'Panduan digital yang aman untuk pemula',
                    'File siap pakai setelah checkout tervalidasi',
                ] as $focus)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">{{ $focus }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-10 md:py-12">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div class="max-w-3xl">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Rekomendasi Utama</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Produk Paling Direkomendasikan Saat Ini</h2>
            </div>
            <a href="{{ route('products.index', ['category' => 'cpns-pppk']) }}" class="font-semibold text-blue-600 hover:text-blue-700">
                Lihat produk prioritas ->
            </a>
        </div>

        @if ($recommendedNowProducts->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($recommendedNowProducts as $product)
                    @include('components.public.product-card', [
                        'product' => $product,
                        'supportWhatsapp' => $landing['support_whatsapp'] ?? null,
                        'whatsappCtaText' => $landing['whatsapp_cta_text'] ?? 'Tanya via WhatsApp',
                        'whatsappDefaultMessage' => $landing['whatsapp_default_message'] ?? null,
                    ])
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="bg-slate-50 py-10 md:py-12">
    <div class="mx-auto max-w-7xl px-6">
        <div class="overflow-hidden rounded-3xl border border-blue-100 bg-gradient-to-br from-slate-950 via-blue-950 to-sky-700 shadow-sm">
            <div class="grid gap-6 px-6 py-8 md:px-8 md:py-10 lg:grid-cols-[minmax(0,1.2fr)_minmax(280px,360px)] lg:items-center">
                <div class="max-w-3xl">
                    <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-widest text-sky-100">
                        Info PPPK 2026
                    </p>
                    <h2 class="mt-4 text-3xl font-black tracking-tight text-white md:text-4xl">
                        PPPK Tendik Sekolah Rakyat 2026 Dibuka
                    </h2>
                    <p class="mt-4 text-sm leading-7 text-slate-200 md:text-base md:leading-8">
                        Kementerian Sosial RI membuka 5.127 formasi PPPK Tenaga Kependidikan Sekolah Rakyat. Cek jadwal, formasi, syarat, dan checklist dokumen.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        @foreach ([
                            '5.127 Formasi',
                            'Pendaftaran 8-14 Juni 2026',
                            'SLTA/D-III/S-1/D-IV',
                        ] as $highlight)
                            <div class="rounded-3xl border border-white/10 bg-white/10 px-4 py-3 text-sm font-semibold text-white">
                                {{ $highlight }}
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ $pppkTendikUrl }}" class="inline-flex items-center justify-center rounded-3xl bg-yellow-400 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-yellow-300">
                            Lihat Info Lengkap
                        </a>
                        <a href="{{ $pppkTendikUrl }}#checklist" class="inline-flex items-center justify-center rounded-3xl border border-white/20 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                            Cek Checklist Dokumen
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-sm backdrop-blur md:p-6">
                    <p class="text-sm font-bold uppercase tracking-widest text-yellow-300">Ringkas</p>
                    <div class="mt-4 space-y-3">
                        @foreach ([
                            ['label' => 'Formasi utama', 'value' => 'Wali Asuh dan Wali Asrama'],
                            ['label' => 'Pendaftaran', 'value' => '8-14 Juni 2026'],
                            ['label' => 'Tujuan halaman', 'value' => 'Bantu cek info dan siapkan dokumen'],
                        ] as $item)
                            <div class="rounded-3xl border border-white/10 bg-slate-950/30 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-sky-100">{{ $item['label'] }}</p>
                                <p class="mt-2 text-sm font-bold leading-6 text-white">{{ $item['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Produk Unggulan</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">{{ $landing['featured_section_title'] }}</h2>
                <p class="mt-3 max-w-2xl text-slate-600">{{ $landing['featured_section_subtitle'] }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Lihat semua produk -></a>
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
                <p class="mt-2 text-slate-600">Produk unggulan akan segera ditampilkan.</p>
            </div>
        @endif
    </div>
</section>

<section class="bg-slate-50 py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Trust</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Transaksi Digital yang Jelas dan Aman</h2>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'File digital siap unduh', 'desc' => 'Produk dikirim dalam format digital yang siap diakses setelah order valid.'],
                ['title' => 'Pembayaran manual aman', 'desc' => 'Pembayaran dicek manual oleh admin untuk memastikan data order sesuai.'],
                ['title' => 'Akses lewat Ruang Akses', 'desc' => 'Setelah pembayaran disetujui admin, pembeli membuka produk dari Ruang Akses memakai email dan invoice.'],
                ['title' => 'Bantuan jika file bermasalah', 'desc' => 'Tim support membantu pengecekan bila file tidak bisa dibuka selama data order valid.'],
            ] as $trust)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900">{{ $trust['title'] }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $trust['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="kategori-utama" class="scroll-mt-24 bg-slate-50 py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Kategori Utama</p>
            <p class="mt-2 text-sm font-semibold text-slate-600">Cocok Untuk Siapa</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Pilih Jalur Belajar dan Kerja Sesuai Kebutuhan</h2>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-5">
            @foreach ([
                'CPNS & PPPK',
                'Administrasi Kerja',
                'Skill Digital Pemula',
                'Template Produktivitas',
                'Aplikasi Siap Pakai',
            ] as $category)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <p class="text-base font-bold text-slate-900">{{ $category }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Kenapa Ruang Cerdas</p>
            <p class="mt-2 text-sm font-semibold text-slate-600">Kenapa aman membeli di Ruang Cerdas?</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Belajar Praktis, Kerja Lebih Rapi, dan Hemat Waktu</h2>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'Materi aplikatif', 'desc' => 'Fokus pada kebutuhan nyata untuk belajar dan kerja harian.'],
                ['title' => 'Format siap pakai', 'desc' => 'Template, checklist, dan panduan bisa langsung digunakan.'],
                ['title' => 'Aman untuk pembeli', 'desc' => 'Akses file diberikan setelah verifikasi pembayaran admin.'],
                ['title' => 'Dukungan responsif', 'desc' => 'Tim support siap membantu pertanyaan seputar produk dan akses.'],
            ] as $benefit)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900">{{ $benefit['title'] }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $benefit['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="cara-beli" class="scroll-mt-24 bg-slate-50 py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Cara Beli</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Langkah Pembelian Sederhana dan Jelas</h2>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['1', 'Pilih produk', 'Buka katalog dan pilih produk yang sesuai kebutuhan Anda.'],
                ['2', 'Isi checkout', 'Masukkan nama, email aktif, dan nomor WhatsApp.'],
                ['3', 'Bayar & upload bukti', 'Lakukan transfer manual lalu upload bukti pembayaran.'],
                ['4', 'Buka Ruang Akses', 'Setelah order paid, pembeli membuka produk dengan email dan invoice yang sama.'],
            ] as $step)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-base font-bold text-white">{{ $step[0] }}</div>
                    <h3 class="mt-4 text-lg font-bold">{{ $step[1] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if (($testimonials ?? collect())->isNotEmpty())
<section class="bg-white py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Testimoni</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Pengguna merasa terbantu dengan produk Ruang Cerdas</h2>
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

@if (($latestArticles ?? collect())->isNotEmpty())
<section class="bg-white py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Artikel Gratis</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Panduan Terbaru dari Ruang Cerdas</h2>
            </div>
            <a href="{{ route('articles.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Lihat semua artikel -></a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($latestArticles as $article)
                <article class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">
                        <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600">{{ $article->title }}</a>
                    </h3>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $article->published_at?->format('d M Y') }}</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 130) }}</p>
                    <a href="{{ route('articles.show', $article) }}" class="mt-4 inline-flex text-sm font-bold text-blue-600 hover:text-blue-700">Baca artikel -></a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if (($leadMagnets ?? collect())->isNotEmpty())
<section class="bg-slate-50 py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Panduan Gratis</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Unduh Materi Gratis</h2>
            </div>
            <a href="{{ route('lead-magnets.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Lihat semua panduan -></a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($leadMagnets as $leadMagnet)
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">
                        <a href="{{ route('lead-magnets.show', $leadMagnet) }}" class="hover:text-blue-600">{{ $leadMagnet->title }}</a>
                    </h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $leadMagnet->description ?: 'Panduan gratis untuk membantu belajar dan kerja lebih terarah.' }}</p>
                    <a href="{{ route('lead-magnets.show', $leadMagnet) }}" class="mt-4 inline-flex text-sm font-bold text-blue-600 hover:text-blue-700">Download Panduan Gratis -></a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section id="faq" class="bg-slate-50 py-12 md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">FAQ Ringkas</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Pertanyaan yang sering ditanyakan</h2>
            <a href="{{ route('public.faq') }}" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat FAQ lengkap -></a>
        </div>

        <div class="mx-auto mt-8 grid max-w-5xl gap-4">
            @foreach ([
                ['q' => 'Bagaimana cara mendapatkan file produk?', 'a' => 'Setelah pembayaran diverifikasi admin dan order berstatus paid, produk dapat dibuka melalui Ruang Akses menggunakan email pembeli dan nomor invoice.'],
                ['q' => 'Apakah produk cocok untuk pemula?', 'a' => 'Ya. Banyak produk Ruang Cerdas disusun untuk membantu pemula mulai dengan lebih terarah.'],
                ['q' => 'Apakah ada produk untuk persiapan CPNS/PPPK?', 'a' => 'Ada. Anda dapat memulai dari kategori CPNS & PPPK untuk memilih materi yang paling sesuai.'],
            ] as $faq)
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="font-bold text-slate-900">{{ $faq['q'] }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $faq['a'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
