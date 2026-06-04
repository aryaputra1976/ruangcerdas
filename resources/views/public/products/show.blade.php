@extends('layouts.public')

@section('title', $product->name . ' - Ruang Cerdas')
@section('meta_description', $product->short_description ?: 'Produk digital Ruang Cerdas siap pakai.')
@section('meta_keywords', $product->category?->name ? ($product->category->name . ', produk digital, ruang cerdas') : 'produk digital, ruang cerdas')
@section('og_type', 'product')
@section('canonical', isset($isPreview) && $isPreview ? route('admin.products.preview', $product) : route('products.show', $product->slug))
@section('og_url', isset($isPreview) && $isPreview ? route('admin.products.preview', $product) : route('products.show', $product->slug))
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

<section class="bg-slate-50 py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        @if ($isPreview)
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-black uppercase tracking-wide text-amber-700">Mode Preview Admin</p>
                <p class="mt-1 text-sm text-amber-900">Halaman ini hanya terlihat oleh admin.</p>
            </div>
        @endif

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                Kembali ke katalog produk
            </a>
            @if ($product->category)
                <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">{{ $product->category->name }}</span>
            @endif
        </div>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.08fr)_minmax(320px,420px)] lg:items-start">
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Produk Digital</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">{{ $product->name }}</h1>
                    <p class="mt-4 text-base leading-7 text-slate-600 md:text-lg md:leading-8">{{ $product->short_description ?: 'Produk digital siap pakai untuk belajar dan kerja lebih terarah.' }}</p>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
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

            <div class="space-y-6 lg:sticky lg:top-24">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">{{ $priceLabel }}</p>
                    @if ($isDiscounted && $normalPrice > $price)
                        <p class="mt-2 text-base text-slate-400 line-through">{{ \App\Support\Money::rupiah($normalPrice) }}</p>
                    @else
                        <p class="mt-2 text-sm font-semibold text-slate-500">Harga Normal</p>
                    @endif
                    <p class="mt-1 text-4xl font-black text-slate-950">{{ \App\Support\Money::rupiah($price) }}</p>

                    @if ($remainingQuota > 0)
                        <p class="mt-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                            Harga pembeli pertama aktif. Tersisa {{ $remainingQuota }} slot.
                        </p>
                    @endif

                    @if ($canCheckout)
                        <a href="{{ route('checkout.create', $product->slug) }}" onclick="window.rcTrack && window.rcTrack('InitiateCheckout', {content_type: 'product', content_ids: [{{ $product->id }}], value: {{ (int) $price }}, currency: 'IDR'});" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white hover:bg-blue-700">
                            Beli Sekarang
                        </a>
                    @else
                        <span class="mt-5 inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl bg-slate-300 px-6 py-4 text-base font-bold text-slate-600">
                            Produk belum tersedia untuk checkout.
                        </span>
                    @endif

                    <a href="{{ route('public.order-tracking.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-4 text-base font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                        Cek Status Order
                    </a>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-slate-950">Ringkasan Manfaat</h2>
                    <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-700">
                        <li>Belajar dan bekerja lebih terarah dengan materi praktis.</li>
                        <li>Format siap pakai sehingga tidak perlu mulai dari nol.</li>
                        <li>Akses file aman setelah pembayaran disetujui admin.</li>
                    </ul>
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

<section class="bg-white py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-6 space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
            <h2 class="text-2xl font-black text-slate-950">Masalah yang sering dialami pembeli</h2>
            <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-700">
                <li>Sulit memulai karena belum punya format kerja atau belajar yang rapi.</li>
                <li>Butuh materi praktis, bukan teori yang terlalu panjang.</li>
                <li>Ingin file siap pakai agar hemat waktu dan langsung digunakan.</li>
            </ul>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h2 class="text-2xl font-black text-slate-950">Solusi dari produk ini</h2>
            <div class="mt-4 prose prose-slate max-w-none text-slate-600 leading-8">
                @if ($product->description)
                    {!! nl2br(e($product->description)) !!}
                @else
                    <p>Produk ini dirancang untuk membantu Anda belajar dan bekerja lebih efektif dengan format yang praktis dan siap digunakan.</p>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6">
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

            <div class="rounded-3xl border border-slate-200 bg-white p-6">
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

<section class="bg-slate-50 py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h2 class="text-2xl font-black text-slate-950">Cocok untuk siapa</h2>
            <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-700">
                <li>Pemula yang ingin belajar lebih terarah</li>
                <li>Calon peserta CPNS/PPPK</li>
                <li>ASN/staf administrasi</li>
                <li>Pekerja kantor yang butuh template praktis</li>
                <li>Pengguna yang ingin file siap pakai</li>
            </ul>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h2 class="text-2xl font-black text-slate-950">Cara mendapatkan file</h2>
            <ol class="mt-4 space-y-3 text-sm leading-7 text-slate-700">
                @foreach ([
                    'Pilih produk',
                    'Checkout',
                    'Bayar manual',
                    'Upload bukti pembayaran',
                    'Admin menyetujui pembayaran',
                    'Link download dikirim ke email',
                ] as $index => $step)
                    <li class="rounded-2xl bg-slate-50 px-4 py-3"><span class="font-bold">{{ $index + 1 }}.</span> {{ $step }}</li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

<section class="bg-white py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-blue-200 bg-blue-50 p-6 md:p-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Garansi Akses File</p>
            <p class="mt-3 text-sm leading-7 text-slate-700 md:text-base">
                Jika file rusak atau link bermasalah, pembeli dapat menghubungi admin untuk dibantu selama data order valid.
            </p>
            <a href="{{ route('public.faq') }}" class="mt-4 inline-flex items-center rounded-2xl border border-blue-300 bg-white px-5 py-3 text-sm font-bold text-blue-700 hover:bg-blue-100">
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
                <a href="{{ route('checkout.create', $product->slug) }}" class="rounded-2xl bg-blue-600 px-7 py-4 text-base font-bold text-white hover:bg-blue-700">Beli Sekarang</a>
            @endif
            @if ($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-500 px-7 py-4 text-base font-bold text-white hover:border-green-400">{{ $waCtaText }}</a>
            @endif
        </div>
    </div>
</section>

<div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 p-3 backdrop-blur md:hidden">
    <div class="mx-auto flex max-w-7xl gap-2">
        @if ($canCheckout)
            <a href="{{ route('checkout.create', $product->slug) }}" onclick="window.rcTrack && window.rcTrack('HeroCtaClick', {source: 'sticky_product_checkout', content_type: 'product', content_ids: [{{ $product->id }}]});" class="inline-flex flex-1 items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white">
                Beli Sekarang
            </a>
        @endif
        @if ($waUrl)
            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" onclick="window.rcTrack && window.rcTrack('Contact', {source: 'sticky_product_whatsapp'});" class="inline-flex flex-1 items-center justify-center rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white">
                WhatsApp
            </a>
        @endif
    </div>
</div>
@endsection
