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
    @endphp
    @include('public.partials.schema.organization', ['supportNumber' => $supportNumber])
@endsection

@section('content')
<section class="relative overflow-hidden bg-slate-50">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,#dbeafe,transparent_45%),radial-gradient(circle_at_bottom_right,#dcfce7,transparent_35%)]"></div>
    <div class="mx-auto grid max-w-7xl gap-8 px-6 py-14 md:py-16 lg:grid-cols-2 lg:items-center">
        <div class="max-w-2xl">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">{{ $landing['hero_badge'] }}</p>
            <h1 class="mt-3 text-4xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-5xl">
                {{ $landing['hero_title'] }}
            </h1>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $landing['hero_subtitle'] }}</p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $landing['primary_cta_url'] }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-7 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                    {{ $landing['primary_cta_text'] }}
                </a>
                <a href="{{ $landing['secondary_cta_url'] }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-7 py-4 text-base font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                    {{ $landing['secondary_cta_text'] }}
                </a>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Fokus Utama Ruang Cerdas</p>
            <h2 class="mt-3 text-2xl font-black text-slate-950">Platform Produk Digital Edukasi Praktis</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    'Persiapan CPNS & PPPK lebih terarah',
                    'Administrasi kerja lebih rapi dan cepat',
                    'Belajar skill digital dari level pemula',
                    'Template dan panduan siap langsung dipakai',
                ] as $focus)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">{{ $focus }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-14 md:py-16">
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

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Trust</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Transaksi Digital yang Jelas dan Aman</h2>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'File digital siap unduh', 'desc' => 'Produk dikirim dalam format digital yang siap diakses setelah order valid.'],
                ['title' => 'Pembayaran manual aman', 'desc' => 'Pembayaran dicek manual oleh admin untuk memastikan data order sesuai.'],
                ['title' => 'Link dikirim setelah disetujui', 'desc' => 'Akses download dikirim ke email setelah pembayaran disetujui admin.'],
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

<section id="kategori-utama" class="scroll-mt-24 bg-slate-50 py-14 md:py-16">
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

<section class="bg-white py-14 md:py-16">
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

<section id="cara-beli" class="scroll-mt-24 bg-slate-50 py-14 md:py-16">
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
                ['4', 'Terima link download', 'Setelah order paid, link aman dikirim ke email pembeli.'],
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
<section class="bg-white py-14 md:py-16">
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
<section class="bg-white py-14 md:py-16">
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
<section class="bg-slate-50 py-14 md:py-16">
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

<section id="faq" class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">FAQ Ringkas</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Pertanyaan yang sering ditanyakan</h2>
            <a href="{{ route('public.faq') }}" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat FAQ lengkap -></a>
        </div>

        <div class="mx-auto mt-8 grid max-w-5xl gap-4">
            @foreach ([
                ['q' => 'Bagaimana cara mendapatkan file produk?', 'a' => 'Setelah pembayaran diverifikasi admin dan order berstatus paid, link download dikirim ke email pembeli.'],
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
