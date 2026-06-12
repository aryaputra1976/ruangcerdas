@extends('layouts.public')

@section('title', 'Panduan Gratis - Ruang Cerdas')
@section('meta_description', 'Unduh panduan gratis dari Ruang Cerdas untuk belajar dan kerja lebih terarah.')
@section('canonical', route('lead-magnets.index'))

@section('content')
<section class="bg-slate-50 pt-2 pb-8 md:pt-3 md:pb-10">
    <div class="mx-auto max-w-7xl px-6">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Panduan Gratis</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Download Panduan Gratis</h1>
            <p class="mt-2 text-slate-600">Kumpulkan panduan ringkas yang bisa langsung dipakai untuk belajar dan kerja lebih terarah.</p>
        </div>

        @if ($leadMagnets->count())
            <div class="mt-5 grid gap-4 md:gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($leadMagnets as $item)
                    <article class="flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        @if ($item->cover_image)
                            <img src="{{ asset('storage/' . $item->cover_image) }}" alt="{{ $item->title }}" class="mb-4 h-44 w-full rounded-2xl object-cover">
                        @endif
                        <h2 class="min-h-[3.5rem] text-xl font-black leading-tight text-slate-950">
                            <a href="{{ route('lead-magnets.show', $item) }}" class="line-clamp-2 hover:text-blue-600">{{ $item->title }}</a>
                        </h2>
                        <p class="mt-3 flex-1 text-sm leading-7 text-slate-600">{{ $item->description ?: 'Panduan gratis untuk membantu belajar dan kerja lebih praktis.' }}</p>
                        <a href="{{ route('lead-magnets.show', $item) }}" class="mt-4 inline-flex text-sm font-bold text-blue-600 hover:text-blue-700">Download Panduan Gratis</a>
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $leadMagnets->links('vendor.pagination.ruangcerdas') }}</div>
        @else
            <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-slate-600">Panduan gratis belum tersedia saat ini.</p>
                <a href="{{ route('products.index') }}" class="rc-btn-primary mt-4 px-5 py-3 text-sm">Lihat Produk</a>
            </div>
        @endif
    </div>
</section>
@endsection
