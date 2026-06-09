@extends('layouts.public')

@section('title', 'Hasil ' . ($tryoutSession->package?->tryout_type_label ?? 'Tryout') . ' - Ruang Cerdas')
@section('meta_description', 'Hasil tryout Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Hasil Tryout</p>
                    <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">{{ $tryoutSession->participant_name }}</h1>
                    <p class="mt-2 text-slate-600">{{ $tryoutSession->package?->title ?? 'Tryout' }}</p>
                </div>
                <div class="rounded-3xl px-5 py-4 text-center {{ $isPassed === null ? 'bg-slate-100 text-slate-700' : ($isPassed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                    <div class="text-xs font-bold uppercase tracking-widest">
                        {{ $isPassed === null ? 'Tanpa Ambang Batas' : ($isPassed ? 'Lulus' : 'Belum Lulus') }}
                    </div>
                    <div class="mt-2 text-2xl font-black">
                        {{ $isPassed === null ? 'Simulasi Selesai' : ($isPassed ? 'Lulus Simulasi' : 'Belum Lulus Simulasi') }}
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($sectionResults as $section)
                    <div class="rounded-3xl bg-blue-50 p-5">
                        <div class="text-sm font-bold uppercase tracking-widest text-blue-700">{{ $section['label'] }}</div>
                        <div class="mt-3 text-3xl font-black text-slate-950">{{ $section['score'] }}</div>
                        <div class="mt-2 text-sm text-slate-600">
                            {{ $section['threshold'] !== null ? 'Minimal ' . $section['threshold'] : 'Tanpa nilai minimum' }}
                        </div>
                    </div>
                @endforeach
                <div class="rounded-3xl bg-slate-950 p-5 text-white">
                    <div class="text-sm font-bold uppercase tracking-widest text-slate-300">Total</div>
                    <div class="mt-3 text-3xl font-black">{{ $tryoutSession->total_score }}</div>
                    <div class="mt-2 text-sm text-slate-300">
                        {{ $totalThreshold !== null ? 'Minimal ' . $totalThreshold : 'Tanpa nilai minimum' }}
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200 p-6">
                    <h2 class="text-xl font-black text-slate-950">Ringkasan Section</h2>
                    <div class="mt-5 grid gap-4">
                        @foreach ($sectionResults as $section)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="text-sm font-semibold text-slate-600">{{ $section['label'] }}</div>
                                <div class="mt-2 text-lg font-black text-slate-950">
                                    @if ($section['correct_count'] === null)
                                        {{ $section['question_count'] }} soal · skor bertingkat
                                    @else
                                        {{ $section['correct_count'] }} benar / {{ $section['incorrect_count'] }} salah
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 p-6">
                    <h2 class="text-xl font-black text-slate-950">Status Kelulusan</h2>
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        @foreach ($sectionResults as $section)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span>
                                    {{ $section['label'] }}
                                    {{ $section['threshold'] !== null ? '>= ' . $section['threshold'] : '' }}
                                </span>
                                <span class="font-bold {{ $section['threshold'] === null ? 'text-slate-600' : ($section['score'] >= $section['threshold'] ? 'text-emerald-600' : 'text-rose-600') }}">
                                    {{ $section['threshold'] === null ? 'Tidak dinilai ambang batas' : ($section['score'] >= $section['threshold'] ? 'Lolos' : 'Belum') }}
                                </span>
                            </div>
                        @endforeach
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span>Total {{ $totalThreshold !== null ? '>= ' . $totalThreshold : '' }}</span>
                            <span class="font-bold {{ $totalThreshold === null ? 'text-slate-600' : ($tryoutSession->total_score >= $totalThreshold ? 'text-emerald-600' : 'text-rose-600') }}">
                                {{ $totalThreshold === null ? 'Tidak dinilai ambang batas' : ($tryoutSession->total_score >= $totalThreshold ? 'Lolos' : 'Belum') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('public.tryout-sessions.review', $tryoutSession) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                    Lihat Pembahasan
                </a>
                <a href="{{ route('public.tryout-sessions.history') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">
                    Riwayat Tryout
                </a>
                <a href="{{ route($tryoutSession->package?->listingRouteName() ?? 'public.tryouts.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">
                    Kembali ke Paket
                </a>
            </div>

            @if ($tryoutSession->package?->is_free)
                <div class="mt-8 rounded-[2rem] border border-blue-100 bg-gradient-to-r from-blue-600 to-sky-500 p-6 text-white shadow-lg md:p-8">
                    <h2 class="text-2xl font-black md:text-3xl">Ingin Latihan Lebih Lengkap?</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-7 text-blue-50 md:text-base">
                        Upgrade ke tryout premium untuk mendapatkan soal lebih banyak, pembahasan, dan evaluasi belajar yang lebih terarah.
                    </p>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route($tryoutSession->package?->listingRouteName() ?? 'public.tryouts.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-blue-700 transition hover:bg-slate-100">
                            Lihat Paket Premium
                        </a>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/40 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/20">
                            Lihat Produk Lain
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
