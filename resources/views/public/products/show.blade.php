@extends('layouts.public')

@section('title', $product->name . ' - Ruang Cerdas')
@section('meta_description', $product->short_description ?: 'Produk digital Ruang Cerdas siap pakai.')
@section('meta_keywords', $product->category?->name ? ($product->category->name . ', produk digital, ruang cerdas') : 'produk digital, ruang cerdas')
@section('og_type', 'product')
@section('canonical', isset($isPreview) && $isPreview ? route('admin.products.preview', $product) : route('products.show', $product->slug))
@section('og_url', isset($isPreview) && $isPreview ? route('admin.products.preview', $product) : route('products.show', $product->slug))
@section('body_class', 'has-mobile-sticky-cta')
@if (isset($isPreview) && $isPreview)
    @section('robots', 'noindex,nofollow')
@endif
@if (!empty($product->cover_image))
    @section('og_image', asset('storage/' . $product->cover_image))
@endif

@section('content')
@php
    $isPreview = $isPreview ?? false;
    $canCheckout = $canCheckout ?? true;

    $price = $pricing['price'] ?? $product->normal_price;
    $normalPrice = $pricing['normal_price'] ?? $product->normal_price;
    $isDiscounted = $pricing['is_discounted'] ?? false;
    $remainingQuota = $pricing['remaining_quota'] ?? 0;
    $priceLabel = $pricing['label'] ?? 'Harga Produk';

    $coverUrl = $product->cover_image ? asset('storage/' . $product->cover_image) : null;

    $benefits = collect(preg_split('/\r\n|\r|\n/', (string) $product->benefits))->map(fn ($item) => trim($item))->filter()->values();
    $contents = collect(preg_split('/\r\n|\r|\n/', (string) $product->contents))->map(fn ($item) => trim($item))->filter()->values();
    $productSignals = strtolower(trim(implode(' ', array_filter([
        $product->name,
        $product->slug,
        $product->short_description,
        $product->description,
        $product->category?->name,
        $product->type,
    ]))));
    $isCpnsOrPppkProduct = str_contains($productSignals, 'cpns') || str_contains($productSignals, 'pppk');
    $isAdminTemplateProduct = str_contains($productSignals, 'administrasi') || str_contains($productSignals, 'template');
    $isSkillProduct = str_contains($productSignals, 'skill') || str_contains($productSignals, 'belajar');

    $packageItems = $contents
        ->reject(fn ($item) => (bool) preg_match('/bonus|free|gratis|tambahan/i', $item))
        ->take(4)
        ->values();
    $bonusItems = $contents
        ->filter(fn ($item) => (bool) preg_match('/bonus|free|gratis|tambahan/i', $item))
        ->values();
    if ($bonusItems->isEmpty()) {
        $bonusItems = $benefits
            ->filter(fn ($item) => (bool) preg_match('/bonus|free|gratis|tambahan/i', $item))
            ->take(2)
            ->values();
    }

    $audienceItems = collect(match (true) {
        $isCpnsOrPppkProduct => [
            'Calon peserta CPNS atau PPPK yang ingin mulai lebih terarah',
            'Pemula yang belum punya roadmap belajar dan file pendukung',
            'Pembeli yang butuh materi ringkas agar tidak bingung mulai dari nol',
        ],
        $isAdminTemplateProduct => [
            'Staf administrasi atau pekerja yang ingin kerja lebih rapi',
            'Pengguna yang butuh template siap edit tanpa membuat dari nol',
            'Pemula yang ingin hasil lebih cepat dengan format yang sudah tertata',
        ],
        $isSkillProduct => [
            'Pemula yang ingin belajar dari langkah yang lebih praktis',
            'Pengguna yang butuh panduan siap ikuti, bukan teori panjang',
            'Pembeli yang ingin file latihan atau template pendukung',
        ],
        default => [
            'Pemula yang ingin mulai lebih terarah',
            'Pengguna yang butuh file digital praktis dan siap pakai',
            'Pembeli yang ingin menghemat waktu saat belajar atau bekerja',
        ],
    })->values();

    $problemItems = collect(match (true) {
        $isCpnsOrPppkProduct => [
            'Sering bingung harus mulai belajar CPNS/PPPK dari bagian mana dulu.',
            'Materi yang ditemukan terpencar sehingga sulit menentukan prioritas.',
            'Butuh file pendukung yang lebih praktis agar proses persiapan terasa lebih ringan.',
        ],
        $isAdminTemplateProduct => [
            'Pekerjaan berulang masih dibuat dari nol sehingga menyita waktu.',
            'Format dokumen belum konsisten dan sering perlu revisi kecil berulang.',
            'Butuh template praktis agar proses kerja lebih cepat dan rapi.',
        ],
        $isSkillProduct => [
            'Belajar terasa berat karena belum punya panduan langkah awal yang jelas.',
            'Sulit menerjemahkan teori menjadi file atau latihan yang benar-benar dipakai.',
            'Butuh materi yang ringkas agar progres tetap jalan walau waktu terbatas.',
        ],
        default => [
            'Sulit mulai karena belum punya alur yang rapi.',
            'Butuh file atau panduan yang langsung bisa dipakai tanpa banyak persiapan.',
            'Ingin proses belajar atau kerja lebih hemat waktu dan tidak berulang dari nol.',
        ],
    })->values();

    $benefitItems = $benefits->take(4);
    if ($benefitItems->isEmpty()) {
        $benefitItems = collect(match (true) {
            $isCpnsOrPppkProduct => [
                'Membantu Anda fokus ke materi dan file yang paling relevan untuk mulai.',
                'Lebih mudah menyusun langkah belajar agar tidak lompat-lompat.',
                'Format digital praktis untuk dipelajari ulang kapan pun dibutuhkan.',
            ],
            $isAdminTemplateProduct => [
                'Mempercepat pekerjaan karena format dasar sudah lebih siap dipakai.',
                'Membantu hasil kerja terlihat lebih rapi dan konsisten.',
                'Mengurangi waktu membuat dokumen dari nol untuk kebutuhan berulang.',
            ],
            default => [
                'Membantu proses belajar atau kerja jadi lebih terarah.',
                'Menghemat waktu karena file sudah disiapkan dalam format digital praktis.',
                'Lebih nyaman dipakai ulang sesuai kebutuhan Anda.',
            ],
        })->values();
    }
    $heroBenefits = $benefitItems->take(3)->values();

    $packageSummary = $packageItems->isNotEmpty()
        ? $packageItems->take(2)->implode(' + ')
        : 'Materi inti digital yang membantu Anda mulai lebih cepat tanpa menyiapkan semuanya dari nol.';
    $bonusSummary = $bonusItems->isNotEmpty()
        ? $bonusItems->take(2)->implode(' + ')
        : 'Bonus mengikuti isi produk yang sedang aktif, dengan fallback materi pendukung praktis saat tersedia.';
    $audienceSummary = $audienceItems->first() ?? 'Cocok untuk pembeli yang ingin mulai lebih terarah dengan file digital praktis.';
    $accessSummary = 'File digital dibuka melalui Ruang Akses setelah pembayaran tervalidasi, menggunakan email pembeli dan nomor invoice.';
    $problemIntro = $product->short_description ?: 'Produk ini dirancang untuk membantu pembeli bergerak lebih cepat dengan materi dan file digital yang praktis.';
    $solutionIntro = trim((string) $product->description) !== ''
        ? trim((string) $product->description)
        : 'Produk ini membantu Anda mulai lebih terarah dengan isi paket yang lebih praktis, manfaat yang jelas, dan format digital yang siap digunakan.';
    $supportNumber = preg_replace('/\D+/', '', (string) ($supportWhatsapp ?? ''));
    if (str_starts_with($supportNumber, '0')) {
        $supportNumber = '62' . substr($supportNumber, 1);
    }

    $defaultWaTemplate = trim((string) ($landingSetting->whatsapp_default_message ?? ''));
    $waTemplate = $defaultWaTemplate !== ''
        ? $defaultWaTemplate
        : 'Halo Ruang Cerdas, saya tertarik dengan produk: {nama}. Harga: {harga}. Link: {url}';
    $waMessage = strtr($waTemplate, [
        '{nama}' => $product->name,
        '{harga}' => \App\Support\Money::rupiah($price),
        '{url}' => url()->current(),
    ]);

    $waUrl = $supportNumber !== '' ? 'https://wa.me/' . $supportNumber . '?text=' . rawurlencode($waMessage) : null;
    $waCtaText = trim((string) ($landingSetting->whatsapp_cta_text ?? '')) ?: 'Hubungi Admin via WhatsApp';
    $allVisibleProductReviews = ($product->relationLoaded('reviews') ? $product->reviews : collect())
        ->filter(fn ($review) => (bool) $review->is_visible)
        ->values();
    $productReviews = $allVisibleProductReviews
        ->take(3)
        ->values();
    $productReviewCount = $allVisibleProductReviews->count();
    $productReviewAverage = $productReviewCount > 0 ? round((float) $allVisibleProductReviews->avg('rating'), 1) : null;
@endphp

@section('schema_jsonld')
    @include('public.partials.schema.product', [
        'product' => $product,
        'price' => $price,
        'canCheckout' => $canCheckout,
        'coverUrl' => $coverUrl,
    ])
    @include('public.partials.schema.breadcrumb', ['product' => $product])
    @if ($product->faqs->isNotEmpty())
        @include('public.partials.schema.faq', ['product' => $product])
    @endif
@endsection

<script>
    window.rcTrack && window.rcTrack('ViewContent', {
        content_type: 'product',
        content_ids: [{{ $product->id }}],
        value: {{ (int) $price }},
        currency: 'IDR'
    });
</script>

<section class="bg-slate-50 pt-4 pb-8 md:pt-5 md:pb-8">
    <div class="mx-auto max-w-7xl px-6">
        @if ($isPreview)
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-black uppercase tracking-wide text-amber-700">Mode Preview Admin</p>
                <p class="mt-1 text-sm text-amber-900">Halaman ini hanya terlihat oleh admin.</p>
            </div>
        @endif

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                Kembali ke Produk
            </a>
            @if ($product->category)
                <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">{{ $product->category->name }}</span>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start">
            <div class="space-y-4">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-7">
                    <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-4xl">{{ $product->name }}</h1>
                    <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600 md:text-lg">{{ $product->short_description ?: 'Produk digital siap pakai untuk belajar dan kerja lebih terarah.' }}</p>

                    @if ($productReviewCount > 0)
                        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                            <div class="inline-flex items-center gap-1 text-amber-500" aria-label="Rating {{ number_format((float) $productReviewAverage, 1, ',', '.') }} dari 5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 {{ $i <= (int) round((float) $productReviewAverage) ? 'opacity-100' : 'opacity-20' }}" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.951-.69l1.069-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <span>Rating {{ number_format((float) $productReviewAverage, 1, ',', '.') }}/5 dari {{ number_format($productReviewCount, 0, ',', '.') }} review pembeli</span>
                        </div>
                    @endif

                    <div class="mt-5 grid gap-3 md:grid-cols-3">
                        @foreach ($heroBenefits as $benefitItem)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                {{ $benefitItem }}
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 overflow-hidden rounded-[1.75rem] border border-slate-200 bg-slate-50 shadow-sm">
                        <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-blue-50 via-white to-emerald-50">
                            @if ($coverUrl)
                                <img src="{{ $coverUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="text-center">
                                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-3xl bg-blue-600 text-3xl font-black text-white">RC</div>
                                    <p class="mt-3 text-sm font-semibold text-slate-500">Ruang Cerdas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-5 lg:sticky lg:top-20">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-blue-600">{{ $priceLabel }}</p>
                    @if ($isDiscounted && $normalPrice > $price)
                        <p class="mt-1 text-base text-slate-400 line-through">{{ \App\Support\Money::rupiah($normalPrice) }}</p>
                    @else
                        <p class="mt-1 text-sm font-semibold text-slate-500">Harga Normal</p>
                    @endif
                    <p class="mt-1 text-4xl font-black text-slate-950">{{ \App\Support\Money::rupiah($price) }}</p>

                    @if ($remainingQuota > 0)
                        <p class="mt-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                            Harga pembeli pertama aktif. Tersisa {{ $remainingQuota }} slot.
                        </p>
                    @endif

                    @if ($canCheckout)
                        <a href="{{ route('checkout.create', $product->slug) }}" onclick="window.rcTrack && window.rcTrack('InitiateCheckout', {content_type: 'product', content_ids: [{{ $product->id }}], value: {{ (int) $price }}, currency: 'IDR'});" class="rc-btn-primary mt-5 w-full px-6 py-4 text-base">
                            Beli Sekarang
                        </a>
                    @else
                        <span class="mt-5 inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl bg-slate-300 px-6 py-4 text-base font-bold text-slate-600">
                            Produk belum tersedia untuk checkout.
                        </span>
                    @endif

                    <a href="{{ route('public.order-tracking.index') }}" class="rc-btn-neutral mt-3 w-full px-6 py-4 text-base">
                        Cek Status Order
                    </a>

                    <div class="mt-5 space-y-3 rounded-3xl bg-slate-50 p-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Isi paket utama</p>
                            <p class="mt-1 text-sm leading-6 text-slate-700">{{ $packageSummary }}</p>
                        </div>
                        <div class="border-t border-slate-200 pt-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Bonus</p>
                            <p class="mt-1 text-sm leading-6 text-slate-700">{{ $bonusSummary }}</p>
                        </div>
                        <div class="border-t border-slate-200 pt-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Cocok untuk siapa</p>
                            <p class="mt-1 text-sm leading-6 text-slate-700">{{ $audienceSummary }}</p>
                        </div>
                        <div class="border-t border-slate-200 pt-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Akses file digital</p>
                            <p class="mt-1 text-sm leading-6 text-slate-700">{{ $accessSummary }}</p>
                        </div>
                    </div>

                    <p class="mt-4 text-xs leading-6 text-slate-500">Akses produk dibuka lewat Ruang Akses setelah pembayaran tervalidasi admin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@if ($product->previewImages->isNotEmpty())
<section class="bg-white py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-6 text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Preview Produk</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Lihat Contoh Isi Produk</h2>
        </div>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($product->previewImages as $previewImage)
                <figure class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
                    <img src="{{ asset('storage/' . $previewImage->image_path) }}"
                         alt="{{ $previewImage->title ?: $product->name }}"
                         loading="lazy"
                         class="h-52 w-full object-cover">
                    @if ($previewImage->title || $previewImage->caption)
                        <figcaption class="p-4">
                            @if ($previewImage->title)
                                <p class="text-base font-bold text-slate-900">{{ $previewImage->title }}</p>
                            @endif
                            @if ($previewImage->caption)
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $previewImage->caption }}</p>
                            @endif
                        </figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-white py-8 md:py-10">
    <div class="mx-auto max-w-7xl px-6 space-y-6">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 md:p-8">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Tentang Produk</p>
                <h2 class="mt-3 text-2xl font-black text-slate-950">Singkat, jelas, dan langsung bisa dipakai</h2>
                <p class="mt-4 text-base leading-7 text-slate-600">{{ $problemIntro }}</p>
                <div class="mt-4 prose prose-slate max-w-none text-slate-600 leading-7">
                    {!! nl2br(e($solutionIntro)) !!}
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6 md:p-8">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Cocok Untuk</p>
                <h2 class="mt-3 text-2xl font-black text-slate-950">Pembeli yang ingin hasil lebih cepat</h2>
                <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-700">
                    @foreach ($audienceItems as $audienceItem)
                        <li class="rounded-2xl bg-white px-4 py-3">{{ $audienceItem }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6">
                <h2 class="text-2xl font-black text-slate-950">Isi Paket</h2>
                @if ($contents->isNotEmpty())
                    <ul class="mt-4 space-y-3">
                        @foreach ($contents as $content)
                            <li class="rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-7 text-slate-700">{{ $content }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-7 text-slate-600">Detail isi paket belum diisi.</p>
                @endif
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6">
                <h2 class="text-2xl font-black text-slate-950">Manfaat Produk</h2>
                @if ($benefits->isNotEmpty())
                    <ul class="mt-4 space-y-3">
                        @foreach ($benefits as $benefit)
                            <li class="rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-7 text-slate-700">{{ $benefit }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-7 text-slate-600">Produk ini membantu menyederhanakan proses belajar dan kerja agar lebih efisien.</p>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-8 md:py-10">
    <div class="mx-auto max-w-7xl px-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6">
            <h2 class="text-2xl font-black text-slate-950">Cara mendapatkan file</h2>
            <ol class="mt-4 space-y-3 text-sm leading-7 text-slate-700">
                @foreach ([
                    'Pilih produk',
                    'Checkout',
                    'Bayar manual',
                    'Upload bukti pembayaran',
                    'Admin menyetujui pembayaran',
                    'Buka lewat Ruang Akses',
                ] as $index => $step)
                    <li class="rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold">{{ $index + 1 }}.</span> {{ $step }}</li>
                @endforeach
            </ol>
        </div>

        <div class="rounded-[2rem] border border-blue-200 bg-blue-50 p-6 md:p-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Aman untuk Pembeli</p>
            <h2 class="mt-3 text-2xl font-black text-slate-950">Ada jalur bantuan kalau akses file bermasalah</h2>
            <p class="mt-3 text-sm leading-7 text-slate-700 md:text-base">
                Jika file rusak atau link bermasalah, pembeli dapat menghubungi admin untuk dibantu selama data order valid.
            </p>
            <a href="{{ route('public.faq') }}" class="mt-5 inline-flex items-center rounded-2xl border border-blue-300 bg-white px-5 py-3 text-sm font-bold text-blue-700 hover:bg-blue-100">
                Lihat FAQ Umum
            </a>
        </div>
    </div>
</section>

@if ($product->faqs->isNotEmpty())
<section class="bg-white py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">FAQ Produk</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Pertanyaan tentang produk ini</h2>
            <a href="{{ route('public.faq') }}" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:text-blue-700">
                Lihat FAQ umum Ruang Cerdas ->
            </a>
            <div class="mt-5 space-y-3">
                @foreach ($product->faqs as $faq)
                    <details class="group rounded-2xl border border-slate-200 bg-white px-5 py-4">
                        <summary class="flex cursor-pointer list-none items-start justify-between gap-4 font-bold text-slate-900">
                            <span>{{ $faq->question }}</span>
                            <span class="mt-1 text-slate-400 transition group-open:rotate-45">+</span>
                        </summary>
                        <p class="mt-3 border-t border-slate-100 pt-3 text-sm leading-6 text-slate-600">{{ $faq->answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@if ($productReviews->isNotEmpty())
<section class="bg-white py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Testimoni Pembeli</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Review dari pembeli produk ini</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Rating rata-rata {{ number_format((float) $productReviewAverage, 1, ',', '.') }}/5 dari {{ number_format($productReviewCount, 0, ',', '.') }} review pembeli.
            </p>
        </div>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($productReviews as $review)
                <article class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-center gap-1 text-amber-500" aria-label="Rating {{ (int) $review->rating }} dari 5">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 {{ $i <= (int) $review->rating ? 'opacity-100' : 'opacity-20' }}" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.951-.69l1.069-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    @if ($review->title)
                        <h3 class="mt-3 text-lg font-black text-slate-950">{{ $review->title }}</h3>
                    @endif
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $review->body }}</p>
                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <p class="font-bold text-slate-900">{{ $review->author_name }}</p>
                        <p class="text-xs text-slate-500">{{ ($review->reviewed_at ?? $review->created_at)?->format('d M Y') }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if (($testimonials ?? collect())->isNotEmpty())
<section class="bg-white pb-14 md:pb-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Testimoni</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Pengalaman pembeli Ruang Cerdas</h2>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($testimonials as $testimonial)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-center gap-1 text-amber-500" aria-label="Rating {{ (int) $testimonial->rating }} dari 5">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 {{ $i <= (int) $testimonial->rating ? 'opacity-100' : 'opacity-20' }}" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.951-.69l1.069-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $testimonial->content }}</p>
                    <p class="mt-3 font-bold text-slate-900">{{ $testimonial->name }}</p>
                    @if ($testimonial->role)
                        <p class="text-xs text-slate-500">{{ $testimonial->role }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-slate-950 py-12 text-white md:py-14">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-300">Siap Mulai?</p>
        <h2 class="mt-3 text-3xl font-black md:text-4xl">Ambil produk ini dan mulai lebih terarah hari ini</h2>
        <p class="mx-auto mt-3 max-w-2xl text-slate-300">Lanjutkan checkout untuk mengamankan akses produk digital Anda.</p>
        <div class="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
            @if ($canCheckout)
                <a href="{{ route('checkout.create', $product->slug) }}" class="rc-btn-primary px-7 py-4 text-base">Beli Sekarang</a>
            @endif
            @if ($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="rc-btn-success px-7 py-4 text-base">{{ $waCtaText }}</a>
            @endif
        </div>
    </div>
</section>

<div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 p-3 backdrop-blur md:hidden">
    <div class="mx-auto flex max-w-7xl gap-2">
        @if ($canCheckout)
            <a href="{{ route('checkout.create', $product->slug) }}" onclick="window.rcTrack && window.rcTrack('InitiateCheckout', {source: 'sticky_product_checkout', content_type: 'product', content_ids: [{{ $product->id }}], value: {{ (int) $price }}, currency: 'IDR'});" class="rc-btn-primary flex-1 rounded-xl px-4 py-3 text-sm">
                Beli Sekarang
            </a>
        @endif
        @if ($waUrl)
            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" onclick="window.rcTrack && window.rcTrack('Contact', {source: 'sticky_product_whatsapp'});" class="rc-btn-success flex-1 rounded-xl px-4 py-3 text-sm">
                WhatsApp
            </a>
        @endif
    </div>
</div>
@endsection
