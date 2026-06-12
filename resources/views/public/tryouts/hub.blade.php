@extends('layouts.public')

@section('title', 'Tryout - Ruang Cerdas')
@section('meta_description', 'Pilih kategori tryout Ruang Cerdas untuk CPNS, PPPK, dan PPPK Tendik.')
@section('canonical', route('public.tryouts.hub'))

@section('content')
<section class="bg-slate-50 pt-2 pb-3 md:pt-3 md:pb-5">
    <div class="relative mx-auto max-w-7xl px-6">
        <p class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">Tryout</p>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Pilih kategori tryout</h1>
        <p class="mt-2 max-w-2xl text-slate-600">Masuk ke jalur tryout yang sesuai agar paket, akses, dan alur mulainya lebih jelas.</p>
    </div>
</section>

<section class="bg-slate-50 pb-8 md:pb-10">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-4 md:gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($cards as $card)
                <article class="flex h-full flex-col rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl md:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Tryout</p>
                            <h2 class="mt-3 min-h-[3.5rem] text-2xl font-black leading-tight text-slate-950">{{ $card['title'] }}</h2>
                        </div>
                        @if ($card['badge'])
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-amber-700">
                                {{ $card['badge'] }}
                            </span>
                        @endif
                    </div>

                    <p class="mt-3 min-h-[4.5rem] text-sm leading-7 text-slate-600">{{ $card['description'] }}</p>

                    <a href="{{ $card['url'] }}" class="rc-btn-secondary mt-auto w-full px-5 py-3 text-sm">
                        {{ $card['cta'] }}
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
