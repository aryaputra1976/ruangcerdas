@extends('layouts.public')

@section('title', ($article->seo_title ?: $article->title) . ' - Ruang Cerdas')
@section('meta_description', $article->seo_description ?: ($article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 150)))
@section('canonical', route('articles.show', $article))
@if ($article->cover_image)
    @section('og_image', asset('storage/' . $article->cover_image))
@endif

@section('content')
<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-4xl px-6">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600">Kembali ke Artikel</a>

        <h1 class="mt-6 text-4xl font-black tracking-tight text-slate-950">{{ $article->title }}</h1>
        <p class="mt-2 text-sm text-slate-500">{{ $article->published_at?->format('d M Y H:i') }}</p>

        @if ($article->cover_image)
            <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}" class="mt-6 h-auto w-full rounded-3xl border border-slate-200 object-cover">
        @endif

        @if ($article->excerpt)
            <p class="mt-6 text-lg leading-8 text-slate-600">{{ $article->excerpt }}</p>
        @endif

        <article class="prose prose-slate mt-8 max-w-none leading-8">
            {!! nl2br(e($article->content)) !!}
        </article>
    </div>
</section>
@endsection
