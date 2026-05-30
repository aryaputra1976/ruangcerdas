@extends('layouts.public')

@section('title', 'Produk Digital Ruang Cerdas')

@section('content')

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">

        <div class="mb-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                    Produk Digital
                </p>

                <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 md:text-5xl">
                    Katalog Produk Ruang Cerdas
                </h1>

                <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                    Pilih produk digital yang membantu pekerjaan administrasi, dokumen kantor, dan produktivitas berbasis AI.
                </p>
            </div>

            <a href="{{ route('home') }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:border-blue-600 hover:text-blue-600">
                ← Kembali ke Beranda
            </a>
        </div>

        <div class="mb-10 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('products.index') }}" class="grid gap-4 lg:grid-cols-12">

                <div class="lg:col-span-6">
                    <label for="q" class="mb-2 block text-sm font-bold text-slate-700">
                        Cari Produk
                    </label>

                    <input
                        id="q"
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama produk..."
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 focus:border-blue-600 focus:ring-blue-600"
                    >
                </div>

                <div class="lg:col-span-4">
                    <label for="category" class="mb-2 block text-sm font-bold text-slate-700">
                        Kategori
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 focus:border-blue-600 focus:ring-blue-600"
                    >
                        <option value="">Semua Kategori</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3 lg:col-span-2">
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
                    >
                        Filter
                    </button>
                </div>

            </form>

            @if (request()->hasAny(['q', 'category']))
                <div class="mt-4">
                    <a href="{{ route('products.index') }}"
                       class="text-sm font-semibold text-slate-500 hover:text-blue-600">
                        Reset filter
                    </a>
                </div>
            @endif
        </div>

        @if ($products->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    @include('components.public.product-card', [
                        'product' => $product,
                    ])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                    📦
                </div>

                <h2 class="mt-5 text-2xl font-black text-slate-950">
                    Produk belum tersedia
                </h2>

                <p class="mt-3 text-slate-600">
                    Belum ada produk aktif dan publish untuk filter ini.
                </p>

                <a href="{{ route('products.index') }}"
                   class="mt-6 inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">
                    Lihat Semua Produk
                </a>
            </div>
        @endif

    </div>
</section>

@endsection