@extends('layouts.public')

@section('title', $product->name . ' - Ruang Cerdas')

@section('content')
@php
    $price = $pricing['price'] ?? $product->normal_price;
    $normalPrice = $pricing['normal_price'] ?? $product->normal_price;
    $isDiscounted = $pricing['is_discounted'] ?? false;
    $remainingQuota = $pricing['remaining_quota'] ?? 0;
    $priceLabel = $pricing['label'] ?? 'Harga Produk';

    $coverUrl = $product->cover_image
        ? asset('storage/' . $product->cover_image)
        : null;

    $benefits = collect(preg_split('/\r\n|\r|\n/', (string) $product->benefits))
        ->filter()
        ->values();

    $contents = collect(preg_split('/\r\n|\r|\n/', (string) $product->contents))
        ->filter()
        ->values();
@endphp

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">

        <div class="mb-8">
            <a href="{{ route('products.index') }}"
               class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                ← Kembali ke katalog produk
            </a>
        </div>

        <div class="grid gap-10 lg:grid-cols-2 lg:items-start">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-blue-50 to-emerald-50">
                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}"
                             alt="{{ $product->name }}"
                             class="h-full w-full object-cover">
                    @else
                        <div class="text-center">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-600 text-2xl font-black text-white">
                                RC
                            </div>
                            <p class="mt-4 text-sm font-semibold text-slate-500">
                                Ruang Cerdas
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    @if ($product->category)
                        <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">
                            {{ $product->category->name }}
                        </span>
                    @endif

                    @if ($remainingQuota > 0)
                        <span class="inline-flex rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">
                            Sisa {{ $remainingQuota }} slot harga awal
                        </span>
                    @endif
                </div>

                <h1 class="text-4xl font-black tracking-tight text-slate-950 md:text-5xl">
                    {{ $product->name }}
                </h1>

                @if ($product->short_description)
                    <p class="mt-5 text-lg leading-8 text-slate-600">
                        {{ $product->short_description }}
                    </p>
                @endif

                <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                        {{ $priceLabel }}
                    </p>

                    @if ($isDiscounted && $normalPrice > $price)
                        <p class="mt-2 text-base text-slate-400 line-through">
                            {{ \App\Support\Money::rupiah($normalPrice) }}
                        </p>
                    @endif

                    <p class="mt-1 text-4xl font-black text-slate-950">
                        {{ \App\Support\Money::rupiah($price) }}
                    </p>

                    @if ($remainingQuota > 0)
                        <div class="mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                            Harga pembeli pertama masih aktif. Tersisa {{ $remainingQuota }} slot.
                        </div>
                    @endif

                    <a href="{{ route('checkout.create', $product->slug) }}"
                       class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                        Beli Sekarang
                    </a>

                    <p class="mt-4 text-center text-sm text-slate-500">
                        Pembayaran manual transfer/QRIS. Link download aktif setelah pembayaran disetujui admin.
                    </p>
                </div>
            </div>

        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-3">

            <div class="lg:col-span-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">
                        Deskripsi Produk
                    </h2>

                    @if ($product->description)
                        <div class="prose prose-slate mt-5 max-w-none leading-8 text-slate-600">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @else
                        <p class="mt-5 text-slate-600">
                            Deskripsi produk belum tersedia.
                        </p>
                    @endif
                </div>
            </div>

            <div class="space-y-8">

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">
                        Manfaat
                    </h3>

                    @if ($benefits->isNotEmpty())
                        <ul class="mt-5 space-y-3">
                            @foreach ($benefits as $benefit)
                                <li class="flex gap-3 text-sm leading-6 text-slate-600">
                                    <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-black text-emerald-700">
                                        ✓
                                    </span>
                                    <span>{{ $benefit }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 text-sm text-slate-500">
                            Manfaat produk belum diisi.
                        </p>
                    @endif
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black text-slate-950">
                        Isi Paket
                    </h3>

                    @if ($contents->isNotEmpty())
                        <ul class="mt-5 space-y-3">
                            @foreach ($contents as $content)
                                <li class="flex gap-3 text-sm leading-6 text-slate-600">
                                    <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-black text-blue-700">
                                        •
                                    </span>
                                    <span>{{ $content }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 text-sm text-slate-500">
                            Isi paket belum diisi.
                        </p>
                    @endif
                </div>

            </div>

        </div>

    </div>
</section>

@endsection