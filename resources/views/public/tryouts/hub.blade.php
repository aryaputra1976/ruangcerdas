@extends('layouts.public')

@section('title', 'Tryout - Ruang Cerdas')
@section('meta_description', 'Pilih kategori tryout Ruang Cerdas untuk CPNS, PPPK, dan PPPK Tendik.')
@section('canonical', route('public.tryouts.hub'))

@section('content')
<section class="relative overflow-hidden bg-slate-950 py-16 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.35),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.22),_transparent_30%)]"></div>

    <div class="relative mx-auto max-w-7xl px-6">
        <div class="max-w-3xl">
            <p class="inline-flex rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-widest text-blue-100">
                Kategori Tryout
            </p>
            <h1 class="mt-5 text-4xl font-black tracking-tight md:text-5xl">Pilih Jalur Tryout yang Ingin Dipelajari</h1>
            <p class="mt-5 text-base leading-8 text-slate-300 md:text-lg">
                Semua kategori tryout Ruang Cerdas dikumpulkan dalam satu halaman agar lebih mudah dijelajahi dari CPNS sampai PPPK Tendik.
            </p>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($cards as $card)
                <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Tryout</p>
                            <h2 class="mt-3 text-2xl font-black text-slate-950">{{ $card['title'] }}</h2>
                        </div>
                        @if ($card['badge'])
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">
                                {{ $card['badge'] }}
                            </span>
                        @endif
                    </div>

                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $card['description'] }}</p>

                    <a href="{{ $card['url'] }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                        {{ $card['cta'] }}
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
