@extends('layouts.public')

@section('title', $product->name . ' - Ruang Cerdas')
@section('meta_description', $product->short_description ?: 'Produk digital Ruang Cerdas siap pakai.')
@section('og_type', 'product')
@section('canonical', isset($isPreview) && $isPreview ? route('admin.products.preview', $product) : url()->current())
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
@endphp

<section class="bg-slate-50 py-16">
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

        <div class="grid gap-10 lg:grid-cols-2 lg:items-start">
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

                <div class="mt-8 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <h1 class="text-4xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $product->name }}</h1>
                    <p class="mt-5 text-lg leading-8 text-slate-600">{{ $product->short_description ?: 'Produk digital siap pakai untuk kebutuhan kerja profesional.' }}</p>

                    <div class="mt-6 prose prose-slate max-w-none leading-8 text-slate-600">
                        @if ($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <p>Konten detail produk belum tersedia.</p>
                        @endif
                    </div>

                    <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <h3 class="text-base font-black text-slate-950">Highlight Produk</h3>
                        <ul class="mt-3 space-y-2 text-sm leading-7 text-slate-700">
                            <li>File digital siap pakai.</li>
                            <li>Download aman setelah pembayaran disetujui.</li>
                            <li>Cocok untuk kebutuhan kerja profesional.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">{{ $priceLabel }}</p>
                    @if ($isDiscounted && $normalPrice > $price)
                        <p class="mt-2 text-base text-slate-400 line-through">{{ \App\Support\Money::rupiah($normalPrice) }}</p>
                    @endif
                    <p class="mt-1 text-4xl font-black text-slate-950">{{ \App\Support\Money::rupiah($price) }}</p>

                    @if ($remainingQuota > 0)
                        <div class="mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold leading-6 text-emerald-700">
                            Harga pembeli awal masih aktif. Tersisa {{ $remainingQuota }} slot.
                        </div>
                    @endif

                    <div class="mt-5 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600">
                        Pembayaran manual.<br>
                        Link download via email setelah pembayaran disetujui.
                    </div>

                    @if ($canCheckout)
                        <a href="{{ route('checkout.create', $product->slug) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
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
                    <p class="mt-4 text-center text-xs leading-5 text-slate-500">Akses download menggunakan token aman.</p>
                </div>

                <div class="rounded-3xl border border-blue-100 bg-blue-50 p-5 text-sm leading-6 text-blue-900">
                    <p class="font-bold">Aman: download lewat token</p>
                    <p class="mt-1 font-bold">Manual: pembayaran diverifikasi admin</p>
                    <p class="mt-1 font-bold">Praktis: link dikirim ke email</p>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">Yang akan Anda dapatkan</h3>
                    <ul class="mt-5 space-y-3 text-sm leading-6 text-slate-600">
                        <li>File digital sesuai produk.</li>
                        <li>Panduan/informasi penggunaan jika tersedia.</li>
                        <li>Akses download setelah pembayaran valid.</li>
                        <li>Bantuan admin jika link bermasalah.</li>
                    </ul>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">Cara mendapatkan produk</h3>
                    <ol class="mt-5 list-decimal space-y-2 pl-5 text-sm leading-6 text-slate-600">
                        <li>Checkout.</li>
                        <li>Bayar manual dan upload bukti.</li>
                        <li>Admin verifikasi.</li>
                        <li>Link download dikirim email.</li>
                    </ol>
                </div>

                @if ($supportNumber !== '')
                    <a href="https://wa.me/{{ $supportNumber }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                        Hubungi Admin via WhatsApp
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-2">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-black text-slate-950">Manfaat</h3>
                @if ($benefits->isNotEmpty())
                    <ul class="mt-5 space-y-3">
                        @foreach ($benefits as $benefit)
                            <li class="text-sm leading-6 text-slate-600">{{ $benefit }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 text-sm text-slate-600">File digital siap pakai untuk pekerjaan profesional.</p>
                @endif
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-black text-slate-950">Isi Paket</h3>
                @if ($contents->isNotEmpty())
                    <ul class="mt-5 space-y-3">
                        @foreach ($contents as $content)
                            <li class="text-sm leading-6 text-slate-600">{{ $content }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 text-sm text-slate-600">Isi paket belum diisi.</p>
                @endif
            </div>
        </div>
    </div>
</section>

@if ($product->faqs->isNotEmpty())
<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6">
            <h3 class="text-2xl font-black text-slate-950">Pertanyaan tentang produk ini</h3>
            <div class="mt-5 space-y-3">
                @foreach ($product->faqs as $faq)
                    <details class="rounded-2xl border border-slate-200 bg-white p-4">
                        <summary class="cursor-pointer list-none pr-6 font-bold text-slate-900">
                            {{ $faq->question }}
                        </summary>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
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
@endsection
