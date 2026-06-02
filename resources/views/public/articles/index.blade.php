@extends('layouts.public')

@section('title', 'Artikel Gratis - Ruang Cerdas')
@section('meta_description', 'Kumpulan artikel dan panduan gratis seputar belajar, administrasi, dan produktivitas digital.')
@section('canonical', route('articles.index'))

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Artikel Gratis</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950">Panduan Praktis untuk Belajar dan Kerja</h1>
            <p class="mt-3 text-slate-600">Baca artikel edukasi singkat yang membantu Anda belajar lebih terarah dan bekerja lebih rapi.</p>
        </div>

        @if ($articles->count())
            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        @if ($article->cover_image)
                            <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}" class="mb-4 h-44 w-full rounded-2xl object-cover">
                        @endif
                        <h2 class="text-xl font-black text-slate-950">
                            <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600">{{ $article->title }}</a>
                        </h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $article->published_at?->format('d M Y') }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 140) }}</p>
                        <a href="{{ route('articles.show', $article) }}" class="mt-4 inline-flex text-sm font-bold text-blue-600 hover:text-blue-700">Baca artikel -></a>
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $articles->links() }}</div>
        @else
            <div class="mt-8 rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-slate-600">Artikel belum tersedia saat ini.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">Lihat Produk</a>
            </div>
        @endif
    </div>
</section>
@endsection
