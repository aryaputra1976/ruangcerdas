@extends('layouts.public')

@section('title', 'Produk Digital - Ruang Cerdas')
@section('meta_description', 'Katalog produk digital siap pakai dari Ruang Cerdas.')

@section('content')
@php
    $hasFilter = request()->filled('q') || request()->filled('category');
    $totalProducts = method_exists($products, 'total') ? $products->total() : $products->count();
@endphp

<section class="relative overflow-hidden bg-slate-950 py-20 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.35),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.28),_transparent_32%)]"></div>

    <div class="relative mx-auto max-w-7xl px-6">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="inline-flex rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-widest text-blue-100">
                    Produk Digital
                </p>
                <h1 class="mt-6 text-4xl font-black tracking-tight md:text-6xl">Katalog Produk Ruang Cerdas</h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-slate-300 md:text-lg">
                    Pilih produk digital siap pakai untuk mempercepat pekerjaan administrasi, dokumen kantor, template, dan produktivitas berbasis AI.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#katalog" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700">
                        Lihat Katalog
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-white/10 p-6 shadow-2xl backdrop-blur">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-white p-5 text-slate-950">
                        <div class="text-3xl font-black text-blue-600">01</div>
                        <p class="mt-4 text-sm font-bold text-slate-500">Produk</p>
                        <p class="mt-1 text-2xl font-black">{{ number_format($totalProducts, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-3xl bg-white p-5 text-slate-950">
                        <div class="text-3xl font-black text-blue-600">02</div>
                        <p class="mt-4 text-sm font-bold text-slate-500">Download</p>
                        <p class="mt-1 text-2xl font-black">Aman</p>
                    </div>
                    <div class="rounded-3xl bg-white p-5 text-slate-950">
                        <div class="text-3xl font-black text-blue-600">03</div>
                        <p class="mt-4 text-sm font-bold text-slate-500">Format</p>
                        <p class="mt-1 text-2xl font-black">Digital</p>
                    </div>
                </div>
                <div class="mt-4 rounded-3xl bg-white/10 p-5 text-sm leading-7 text-slate-200">
                    Setelah pembayaran disetujui admin, pembeli mendapatkan link download khusus dengan token aman.
                </div>
            </div>
        </div>
    </div>
</section>

<section id="katalog" class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Katalog</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Produk Tersedia</h2>
                <p class="mt-3 max-w-2xl text-slate-600">Gunakan filter untuk menemukan produk yang sesuai kebutuhan.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 shadow-sm">
                Total: <span class="font-black text-slate-950">{{ number_format($totalProducts, 0, ',', '.') }}</span> produk
            </div>
        </div>

        <div class="mb-10 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('products.index') }}" class="grid gap-4 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <label for="q" class="mb-2 block text-sm font-bold text-slate-700">Cari Produk</label>
                    <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, deskripsi, atau isi produk..." class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </div>
                <div class="lg:col-span-4">
                    <label for="category" class="mb-2 block text-sm font-bold text-slate-700">Kategori</label>
                    <select id="category" name="category" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3 lg:col-span-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                        Filter
                    </button>
                </div>
            </form>

            @if ($hasFilter)
                <div class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                    <div class="text-sm text-slate-600">
                        Filter aktif:
                        @if (request('q'))
                            <span class="ml-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">Keyword: {{ request('q') }}</span>
                        @endif
                        @if (request('category'))
                            <span class="ml-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Kategori: {{ request('category') }}</span>
                        @endif
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-bold text-red-600 hover:text-red-700">Reset filter</a>
                </div>
            @endif
        </div>

        @if ($products->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    @include('components.public.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        @else
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-xl font-black text-slate-700">RC</div>
                <h2 class="mt-5 text-2xl font-black text-slate-950">
                    @if ($hasFilter)
                        Produk tidak ditemukan
                    @else
                        Produk belum tersedia.
                    @endif
                </h2>
                <p class="mx-auto mt-3 max-w-md text-slate-600">
                    @if ($hasFilter)
                        Tidak ada produk aktif dan publish yang sesuai dengan filter pencarian Anda.
                    @else
                        Produk belum tersedia.
                    @endif
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    @if ($hasFilter)
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                            Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
