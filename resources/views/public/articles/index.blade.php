@extends('layouts.public')

@section('title', 'Artikel Gratis - Ruang Cerdas')
@section('meta_description', 'Kumpulan artikel dan panduan gratis seputar belajar, administrasi, dan produktivitas digital.')
@section('canonical', route('articles.index'))

@php
    $productsUrl = \Illuminate\Support\Facades\Route::has('products.index') ? route('products.index') : url('/produk');
    $leadMagnetUrl = \Illuminate\Support\Facades\Route::has('lead-magnets.index') ? route('lead-magnets.index') : url('/panduan-gratis');
@endphp

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="max-w-4xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm md:p-10">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">ARTIKEL CPNS & PANDUAN BELAJAR</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 md:text-5xl">Panduan Praktis Persiapan CPNS untuk Pemula</h1>
            <p class="mt-4 max-w-3xl text-slate-600">Baca artikel edukasi seputar persiapan CPNS, latihan soal, tryout, dan strategi belajar mandiri agar persiapan Anda lebih terarah.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $leadMagnetUrl }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                    Download Panduan Gratis
                </a>
                <a href="{{ $productsUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">
                    Lihat CPNS Starter Kit
                </a>
            </div>
        </div>

        @if ($articles->count())
            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <article class="flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        @if ($article->cover_image)
                            <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}" class="mb-4 h-44 w-full rounded-2xl object-cover">
                        @endif
                        <h2 class="text-xl font-black text-slate-950">
                            <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600">{{ $article->title }}</a>
                        </h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $article->published_at?->format('d M Y') }}</p>
                        <p class="mt-3 flex-1 text-sm leading-7 text-slate-600">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 140) }}</p>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                                Baca Artikel
                            </a>
                            <a href="{{ $productsUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">
                                Lihat Produk
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $articles->links() }}</div>

            <div class="mt-10 rounded-[2rem] border border-blue-100 bg-gradient-to-r from-blue-600 to-sky-500 p-8 text-white shadow-lg md:p-10">
                <h2 class="text-2xl font-black md:text-3xl">Masih Bingung Mulai Persiapan CPNS?</h2>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-blue-50 md:text-base">Gunakan CPNS Starter Kit RuangCerdas untuk membantu Anda belajar lebih rapi dari rumah. Berisi eBook, checklist, jadwal belajar, ringkasan materi, worksheet, dan panduan praktis untuk pemula.</p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $productsUrl }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-blue-700 transition hover:bg-slate-100">
                        Lihat CPNS Starter Kit
                    </a>
                    <a href="{{ $leadMagnetUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-white/40 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/20">
                        Download Panduan Gratis
                    </a>
                </div>
            </div>
        @else
            <div class="mt-8 rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
                <p class="mx-auto max-w-2xl text-slate-600">Artikel belum tersedia saat ini. Sambil menunggu, Anda bisa melihat produk CPNS atau mengunduh panduan gratis.</p>
                <div class="mt-5 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ $productsUrl }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">Lihat Produk</a>
                    <a href="{{ $leadMagnetUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600">Download Panduan Gratis</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
