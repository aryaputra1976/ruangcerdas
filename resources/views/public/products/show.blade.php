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

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        @if ($isPreview)
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-black uppercase tracking-wide text-amber-700">Mode Preview Admin</p>
                <p class="mt-1 text-sm text-amber-900">Halaman ini hanya terlihat oleh admin.</p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ ($previewStatus['is_active'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                        {{ ($previewStatus['is_active'] ?? false) ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ ($previewStatus['has_file'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ ($previewStatus['has_file'] ?? false) ? 'File tersedia' : 'File belum ada' }}
                    </span>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ ($previewStatus['is_public_visible'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ ($previewStatus['is_public_visible'] ?? false) ? 'Tampil Public' : 'Tidak Tampil Public' }}
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:border-slate-400">
                        Kembali ke Admin Produk
                    </a>
                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 hover:bg-blue-100">
                        Edit Produk
                    </a>
                    @if ($previewStatus['is_public_visible'] ?? false)
                        <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 hover:bg-emerald-100">
                            Lihat Public
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <div class="mb-8 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:border-blue-600 hover:text-blue-600">
                Kembali ke katalog produk
            </a>

            <div class="flex flex-wrap gap-2">
                @if ($product->category)
                    <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">{{ $product->category->name }}</span>
                @endif
                <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700">Digital</span>
                <span class="inline-flex rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">Siap Download</span>
            </div>
        </div>

        <div class="mb-8 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-center text-sm font-semibold text-slate-700">
            Pembayaran manual • Verifikasi admin • Link download via email • Token aman
        </div>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.08fr)_minmax(320px,420px)] lg:items-start">
            <div>
                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                    <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-blue-50 via-white to-emerald-50">
                        @if ($coverUrl)
                            <img src="{{ $coverUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="text-center">
                                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-blue-600 text-3xl font-black text-white">RC</div>
                                <p class="mt-4 text-sm font-semibold text-slate-500">Ruang Cerdas</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700">Produk Digital</span>
                        @if ($product->category)
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $product->category->name }}</span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">{{ $product->name }}</h1>
                    <p class="mt-4 text-base leading-7 text-slate-600 md:text-lg md:leading-8">{{ $product->short_description ?: 'Produk digital siap pakai untuk kebutuhan kerja profesional.' }}</p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        @foreach ([
                            ['label' => 'Pembayaran', 'value' => 'Manual'],
                            ['label' => 'Verifikasi', 'value' => 'Oleh admin'],
                            ['label' => 'Akses file', 'value' => 'Via email'],
                        ] as $summaryItem)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $summaryItem['label'] }}</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ $summaryItem['value'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-7 prose prose-slate max-w-none leading-8 text-slate-600">
                        @if ($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <p>Konten detail produk belum tersedia.</p>
                        @endif
                    </div>

                    <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <h3 class="text-base font-black text-slate-950">Highlight Produk</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-700">
                            @foreach ([
                                'File digital siap pakai untuk kebutuhan kerja Anda.',
                                'Akses download aktif setelah pembayaran diverifikasi admin.',
                                'Cocok untuk pengguna yang ingin proses lebih praktis dan rapi.',
                            ] as $highlight)
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                        <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                            <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span>{{ $highlight }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-24">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">{{ $priceLabel }}</p>
                    @if ($isDiscounted && $normalPrice > $price)
                        <p class="mt-2 text-base text-slate-400 line-through">{{ \App\Support\Money::rupiah($normalPrice) }}</p>
                    @endif
                    <p class="mt-1 text-4xl font-black text-slate-950 md:text-[2.7rem]">{{ \App\Support\Money::rupiah($price) }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Harga mengikuti promo aktif atau skema harga pembeli awal jika tersedia.</p>

                    @if ($remainingQuota > 0)
                        <div class="mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold leading-6 text-emerald-700">
                            Harga pembeli awal masih aktif. Tersisa {{ $remainingQuota }} slot.
                        </div>
                    @endif

                    <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <div class="grid gap-3 text-sm text-slate-700">
                            @foreach ([
                                'Pembayaran dilakukan manual.',
                                'Pembayaran diverifikasi admin.',
                                'Link download dikirim ke email pembeli.',
                                'Akses file menggunakan token aman.',
                            ] as $trustNote)
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                        <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                            <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span>{{ $trustNote }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($canCheckout)
                        <a href="{{ route('checkout.create', $product->slug) }}" onclick="window.rcTrack && window.rcTrack('InitiateCheckout', {content_type: 'product', content_ids: [{{ $product->id }}], value: {{ (int) $price }}, currency: 'IDR'});" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            Beli Sekarang
                        </a>
                    @else
                        <span class="mt-6 inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl bg-slate-300 px-6 py-4 text-base font-bold text-slate-600">
                            Produk belum tersedia untuk checkout.
                        </span>
                    @endif

                    <a href="{{ route('public.order-tracking.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-4 text-base font-bold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">
                        Cek Order
                    </a>
                    <p class="mt-4 text-center text-xs leading-5 text-slate-500">Akses download menggunakan token aman dan masa berlaku tertentu.</p>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">Yang akan Anda dapatkan</h3>
                    <div class="mt-5 grid gap-3">
                        @foreach ([
                            'File digital sesuai produk yang Anda beli.',
                            'Panduan atau informasi penggunaan jika memang disertakan dalam produk.',
                            'Akses download setelah pembayaran dinyatakan valid.',
                            'Bantuan admin jika ada kendala pada link download.',
                        ] as $deliverable)
                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                    <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                        <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>{{ $deliverable }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">Cara mendapatkan produk</h3>
                    <div class="mt-5 grid gap-3">
                        @foreach ([
                            'Checkout menggunakan data email dan WhatsApp aktif.',
                            'Lakukan pembayaran manual lalu upload bukti pembayaran.',
                            'Admin memverifikasi pembayaran yang masuk.',
                            'Setelah disetujui, link download dikirim ke email pembeli.',
                        ] as $index => $purchaseStep)
                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">{{ $index + 1 }}</span>
                                <span>{{ $purchaseStep }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">Butuh bantuan sebelum membeli?</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Jika ada pertanyaan tentang isi produk, alur pembayaran, atau status order, tim kami siap membantu.</p>
                    <div class="mt-5 flex flex-col gap-3">
                        @if ($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" onclick="window.rcTrack && window.rcTrack('Contact', {source: 'product_detail'});" class="inline-flex w-full items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                                {{ $waCtaText }}
                            </a>
                        @endif
                        <a href="{{ route('public.order-tracking.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">
                            Cek Status Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-2">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-black text-slate-950">Manfaat</h3>
                @if ($benefits->isNotEmpty())
                    <ul class="mt-5 space-y-3">
                        @foreach ($benefits as $benefit)
                            <li class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                    <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                        <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-4 text-sm leading-6 text-slate-600">
                        File digital siap pakai untuk membantu pekerjaan atau aktivitas profesional Anda.
                    </div>
                @endif
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-black text-slate-950">Isi Paket</h3>
                @if ($contents->isNotEmpty())
                    <ul class="mt-5 space-y-3">
                        @foreach ($contents as $content)
                            <li class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                    <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                        <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>{{ $content }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-4 text-sm leading-6 text-slate-600">
                        Detail isi paket belum diisi. Anda tetap dapat melihat ringkasan produk dan menghubungi admin jika perlu konfirmasi.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if ($product->faqs->isNotEmpty())
<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">FAQ Produk</p>
            <h3 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Pertanyaan tentang produk ini</h3>
            <div class="mt-5 space-y-3">
                @foreach ($product->faqs as $faq)
                    <details class="group rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                        <summary class="flex cursor-pointer list-none items-start justify-between gap-4 pr-1 font-bold text-slate-900">
                            <span>{{ $faq->question }}</span>
                            <span class="mt-1 text-slate-400 transition group-open:rotate-45">+</span>
                        </summary>
                        <p class="mt-3 border-t border-slate-100 pt-3 text-sm leading-6 text-slate-600">
                            {{ $faq->answer }}
                        </p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@if (($testimonials ?? collect())->isNotEmpty())
<section class="bg-white pb-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Testimonial</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Testimoni Pembeli</h2>
        </div>
        <div class="grid gap-4 md:gap-6 md:grid-cols-3">
            @foreach ($testimonials as $testimonial)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 md:p-6">
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
