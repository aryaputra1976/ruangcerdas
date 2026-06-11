@extends('layouts.public')

@section('title', 'Panduan Gratis - Ruang Cerdas')
@section('meta_description', 'Unduh panduan gratis dari Ruang Cerdas untuk belajar dan kerja lebih terarah.')
@section('canonical', route('lead-magnets.index'))

@section('content')
<section class="bg-slate-50 pt-4 pb-10 md:pt-6 md:pb-12">
    <div class="mx-auto max-w-7xl px-6">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Panduan Gratis</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Download Panduan Gratis</h1>
        </div>

        @if ($leadMagnets->count())
            <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($leadMagnets as $item)
                    <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        @if ($item->cover_image)
                            <img src="{{ asset('storage/' . $item->cover_image) }}" alt="{{ $item->title }}" class="mb-4 h-44 w-full rounded-2xl object-cover">
                        @endif
                        <h2 class="text-xl font-black text-slate-950">
                            <a href="{{ route('lead-magnets.show', $item) }}" class="hover:text-blue-600">{{ $item->title }}</a>
                        </h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item->description ?: 'Panduan gratis untuk membantu belajar dan kerja lebih praktis.' }}</p>
                        <a href="{{ route('lead-magnets.show', $item) }}" class="mt-4 inline-flex text-sm font-bold text-blue-600 hover:text-blue-700">Download Panduan Gratis</a>
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $leadMagnets->links() }}</div>
        @else
            <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-slate-600">Panduan gratis belum tersedia saat ini.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">Lihat Produk</a>
            </div>
        @endif
    </div>
</section>
@endsection
