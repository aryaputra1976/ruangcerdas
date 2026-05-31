@extends('layouts.public')

@section('title', 'Ruang Cerdas - Produk Digital dan Tools AI')

@section('content')
<section class="relative overflow-hidden bg-slate-50">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,#dbeafe,transparent_40%),radial-gradient(circle_at_bottom_right,#dcfce7,transparent_35%)]"></div>

    <div class="mx-auto max-w-7xl px-6 py-24 md:py-32">
        <div class="mx-auto max-w-4xl text-center">
            <p class="text-sm font-bold uppercase tracking-[0.35em] text-blue-600">
                {{ $landing['hero_badge'] }}
            </p>

            <h1 class="mt-6 text-4xl font-black tracking-tight text-slate-950 md:text-7xl">
                {{ $landing['hero_title'] }}
            </h1>

            <p class="mx-auto mt-6 max-w-3xl text-lg leading-8 text-slate-600">
                {{ $landing['hero_subtitle'] }}
            </p>

            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ $landing['primary_cta_url'] }}" class="rounded-2xl bg-blue-600 px-7 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                    {{ $landing['primary_cta_text'] }}
                </a>

                <a href="{{ $landing['secondary_cta_url'] }}" class="rounded-2xl border border-slate-300 bg-white px-7 py-4 text-base font-bold text-slate-900 hover:border-blue-600 hover:text-blue-600">
                    {{ $landing['secondary_cta_text'] }}
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Produk Unggulan</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">
                    {{ $landing['featured_section_title'] }}
                </h2>
                <p class="mt-3 max-w-2xl text-slate-600">
                    {{ $landing['featured_section_subtitle'] }}
                </p>
            </div>

            <a href="{{ route('products.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">
                Lihat semua produk ->
            </a>
        </div>

        @if ($featuredProducts->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($featuredProducts as $product)
                    @include('components.public.product-card', [
                        'product' => $product,
                        'pricingService' => app(\App\Services\PricingService::class),
                    ])
                @endforeach
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                <h3 class="text-xl font-bold">Produk belum tersedia</h3>
                <p class="mt-2 text-slate-600">Produk Ruang Cerdas akan segera ditampilkan.</p>
            </div>
        @endif
    </div>
</section>

<section id="cara-beli" class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Cara Beli</p>
            <h2 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">
                Pembelian mudah dengan pembayaran manual
            </h2>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-4">
            @foreach ([
                ['1', 'Pilih Produk', 'Buka katalog dan pilih produk digital yang dibutuhkan.'],
                ['2', 'Isi Data', 'Masukkan nama, email aktif, dan nomor WhatsApp.'],
                ['3', 'Bayar Manual', 'Transfer atau bayar melalui QRIS sesuai instruksi invoice.'],
                ['4', 'Download', 'Setelah admin approve, link download produk akan tersedia.'],
            ] as $step)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white">
                        {{ $step[0] }}
                    </div>
                    <h3 class="mt-5 text-lg font-bold">{{ $step[1] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-14">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center">
            <h3 class="text-2xl font-black text-slate-900">{{ $landing['support_title'] }}</h3>
            <p class="mx-auto mt-2 max-w-2xl text-slate-600">{{ $landing['support_text'] }}</p>
            @if (!empty($landing['support_whatsapp']))
                <p class="mt-3 text-sm font-semibold text-slate-700">WhatsApp: {{ $landing['support_whatsapp'] }}</p>
            @endif
            <p class="mt-5 text-sm text-slate-500">{{ $landing['footer_short_text'] }}</p>
        </div>
    </div>
</section>
@endsection
