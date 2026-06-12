@extends('layouts.public')

@section('title', ($article->seo_title ?: $article->title) . ' - Ruang Cerdas')
@section('meta_description', $article->seo_description ?: ($article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 150)))
@section('canonical', route('articles.show', $article))
@if ($article->cover_image)
    @section('og_image', asset('storage/' . $article->cover_image))
@endif

@php
    $productsUrl = \Illuminate\Support\Facades\Route::has('products.index') ? route('products.index') : url('/produk');
    $leadMagnetUrl = \Illuminate\Support\Facades\Route::has('lead-magnets.index') ? route('lead-magnets.index') : url('/panduan-gratis');
@endphp

@section('content')
<section class="bg-white pt-4 pb-8 md:pt-5 md:pb-10">
    <div class="mx-auto max-w-4xl px-6">
        <a href="{{ route('articles.index') }}" class="rc-btn-neutral px-4 py-2 text-sm">Kembali ke Artikel</a>

        <p class="mt-4 text-sm font-bold uppercase tracking-[0.28em] text-blue-600">Artikel</p>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">{{ $article->title }}</h1>
        <p class="mt-2 text-sm text-slate-500">{{ $article->published_at?->format('d M Y H:i') }}</p>

        @if ($article->cover_image)
            <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}" class="mt-5 h-auto w-full rounded-3xl border border-slate-200 object-cover">
        @endif

        @if ($article->excerpt)
            <p class="mt-5 text-lg leading-8 text-slate-600">{{ $article->excerpt }}</p>
        @endif

        <article class="prose prose-slate mt-6 max-w-none leading-8">
            {!! nl2br(e($article->content)) !!}
        </article>

        <div class="mt-8 rounded-[2rem] border border-blue-100 bg-slate-50 p-6 shadow-sm md:p-8">
            <h2 class="text-2xl font-black text-slate-950 md:text-3xl">Ingin Persiapan CPNS yang Lebih Rapi?</h2>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600 md:text-base">CPNS Starter Kit RuangCerdas membantu pemula belajar lebih terarah melalui panduan belajar, checklist, jadwal belajar, ringkasan materi, template catatan, dan bahan pendukung lainnya.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $productsUrl }}" class="rc-btn-primary px-6 py-3 text-sm">
                    Lihat CPNS Starter Kit
                </a>
                <a href="{{ $leadMagnetUrl }}" class="rc-btn-secondary px-6 py-3 text-sm">
                    Download Panduan Gratis
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
