@extends('layouts.public')

@section('title', 'Artikel Gratis - Ruang Cerdas')
@section('meta_description', 'Kumpulan artikel dan panduan gratis seputar belajar, administrasi, dan produktivitas digital.')
@section('canonical', route('articles.index'))

@php
    $productsUrl = \Illuminate\Support\Facades\Route::has('products.index') ? route('products.index') : url('/produk');
    $leadMagnetUrl = \Illuminate\Support\Facades\Route::has('lead-magnets.index') ? route('lead-magnets.index') : url('/panduan-gratis');
@endphp

@section('content')
<section class="bg-slate-50 pt-3 pb-10 md:pt-5 md:pb-12">
    <div class="mx-auto max-w-7xl px-6">
        <div class="max-w-4xl rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Artikel</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Panduan Praktis Persiapan CPNS</h1>
            <p class="mt-2 max-w-2xl text-slate-600">Artikel singkat untuk bantu mulai belajar lebih rapi sebelum masuk ke produk atau tryout.</p>
            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $leadMagnetUrl }}" class="rc-btn-secondary px-6 py-3 text-sm">
                    Download Panduan Gratis
                </a>
                <a href="{{ $productsUrl }}" class="rc-btn-primary px-6 py-3 text-sm">
                    Lihat CPNS Starter Kit
                </a>
            </div>
        </div>

        @if ($articles->count())
            <div class="mt-6 grid gap-4 md:gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <article class="flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        @if ($article->cover_image)
                            <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}" class="mb-4 h-44 w-full rounded-2xl object-cover">
                        @endif
                        <h2 class="min-h-[3.5rem] text-xl font-black leading-tight text-slate-950">
                            <a href="{{ route('articles.show', $article) }}" class="line-clamp-2 hover:text-blue-600">{{ $article->title }}</a>
                        </h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $article->published_at?->format('d M Y') }}</p>
                        <p class="mt-3 flex-1 text-sm leading-7 text-slate-600">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 140) }}</p>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('articles.show', $article) }}" class="rc-btn-secondary px-5 py-3 text-sm">
                                Baca Artikel
                            </a>
                            <a href="{{ $productsUrl }}" class="rc-btn-primary px-5 py-3 text-sm">
                                Lihat Produk
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $articles->links('vendor.pagination.ruangcerdas') }}</div>

            <div class="mt-8 rounded-[2rem] border border-blue-100 bg-gradient-to-r from-blue-600 to-sky-500 p-8 text-white shadow-lg md:p-10">
                <h2 class="text-2xl font-black md:text-3xl">Masih Bingung Mulai Persiapan CPNS?</h2>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-blue-50 md:text-base">Gunakan CPNS Starter Kit RuangCerdas untuk membantu Anda belajar lebih rapi dari rumah. Berisi eBook, checklist, jadwal belajar, ringkasan materi, worksheet, dan panduan praktis untuk pemula.</p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $productsUrl }}" class="rc-btn-primary px-6 py-3 text-sm">
                        Lihat CPNS Starter Kit
                    </a>
                    <a href="{{ $leadMagnetUrl }}" class="rc-btn-neutral px-6 py-3 text-sm">
                        Download Panduan Gratis
                    </a>
                </div>
            </div>
        @else
            <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
                <p class="mx-auto max-w-2xl text-slate-600">Artikel belum tersedia saat ini. Sambil menunggu, Anda bisa melihat produk CPNS atau mengunduh panduan gratis.</p>
                <div class="mt-5 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ $productsUrl }}" class="rc-btn-primary px-5 py-3 text-sm">Lihat Produk</a>
                    <a href="{{ $leadMagnetUrl }}" class="rc-btn-secondary px-5 py-3 text-sm">Download Panduan Gratis</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
