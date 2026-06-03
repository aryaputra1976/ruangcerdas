@extends('layouts.public')

@section('title', 'Hasil Tryout CPNS - Ruang Cerdas')
@section('meta_description', 'Hasil tryout CPNS Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Hasil Tryout</p>
                    <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">{{ $tryoutSession->participant_name }}</h1>
                    <p class="mt-2 text-slate-600">{{ $tryoutSession->package?->title ?? 'Tryout CPNS' }}</p>
                </div>
                <div class="rounded-3xl px-5 py-4 text-center {{ $isPassed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                    <div class="text-xs font-bold uppercase tracking-widest">{{ $isPassed ? 'Lulus' : 'Belum Lulus' }}</div>
                    <div class="mt-2 text-2xl font-black">{{ $isPassed ? 'Lulus Simulasi' : 'Belum Lulus Simulasi' }}</div>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl bg-blue-50 p-5">
                    <div class="text-sm font-bold uppercase tracking-widest text-blue-700">TWK</div>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ $tryoutSession->twk_score }}</div>
                    <div class="mt-2 text-sm text-slate-600">Minimal {{ $thresholds['twk'] }}</div>
                </div>
                <div class="rounded-3xl bg-blue-50 p-5">
                    <div class="text-sm font-bold uppercase tracking-widest text-blue-700">TIU</div>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ $tryoutSession->tiu_score }}</div>
                    <div class="mt-2 text-sm text-slate-600">Minimal {{ $thresholds['tiu'] }}</div>
                </div>
                <div class="rounded-3xl bg-blue-50 p-5">
                    <div class="text-sm font-bold uppercase tracking-widest text-blue-700">TKP</div>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ $tryoutSession->tkp_score }}</div>
                    <div class="mt-2 text-sm text-slate-600">Minimal {{ $thresholds['tkp'] }}</div>
                </div>
                <div class="rounded-3xl bg-slate-950 p-5 text-white">
                    <div class="text-sm font-bold uppercase tracking-widest text-slate-300">Total</div>
                    <div class="mt-3 text-3xl font-black">{{ $tryoutSession->total_score }}</div>
                    <div class="mt-2 text-sm text-slate-300">Minimal {{ $thresholds['total'] }}</div>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200 p-6">
                    <h2 class="text-xl font-black text-slate-950">Ringkasan TWK & TIU</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-sm font-semibold text-slate-600">TWK Benar / Salah</div>
                            <div class="mt-2 text-lg font-black text-slate-950">{{ $twkCorrect }} / {{ max($twkCount - $twkCorrect, 0) }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-sm font-semibold text-slate-600">TIU Benar / Salah</div>
                            <div class="mt-2 text-lg font-black text-slate-950">{{ $tiuCorrect }} / {{ max($tiuCount - $tiuCorrect, 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 p-6">
                    <h2 class="text-xl font-black text-slate-950">Status Kelulusan</h2>
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span>TWK >= {{ $thresholds['twk'] }}</span>
                            <span class="font-bold {{ $tryoutSession->twk_score >= $thresholds['twk'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ $tryoutSession->twk_score >= $thresholds['twk'] ? 'Lolos' : 'Belum' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span>TIU >= {{ $thresholds['tiu'] }}</span>
                            <span class="font-bold {{ $tryoutSession->tiu_score >= $thresholds['tiu'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ $tryoutSession->tiu_score >= $thresholds['tiu'] ? 'Lolos' : 'Belum' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span>TKP >= {{ $thresholds['tkp'] }}</span>
                            <span class="font-bold {{ $tryoutSession->tkp_score >= $thresholds['tkp'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ $tryoutSession->tkp_score >= $thresholds['tkp'] ? 'Lolos' : 'Belum' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span>Total >= {{ $thresholds['total'] }}</span>
                            <span class="font-bold {{ $tryoutSession->total_score >= $thresholds['total'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ $tryoutSession->total_score >= $thresholds['total'] ? 'Lolos' : 'Belum' }}</span>
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
                <a href="{{ route('public.tryouts.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">
                    Kembali ke Paket
                </a>
            </div>

            @if ($tryoutSession->package?->is_free)
                <div class="mt-8 rounded-[2rem] border border-blue-100 bg-gradient-to-r from-blue-600 to-sky-500 p-6 text-white shadow-lg md:p-8">
                    <h2 class="text-2xl font-black md:text-3xl">Ingin Latihan Lebih Lengkap?</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-7 text-blue-50 md:text-base">
                        Upgrade ke Tryout Premium untuk mendapatkan soal lebih banyak, pembahasan, dan evaluasi belajar yang lebih terarah.
                    </p>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('public.tryouts.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-blue-700 transition hover:bg-slate-100">
                            Lihat Tryout Premium
                        </a>
                        <a href="{{ \Illuminate\Support\Facades\Route::has('products.index') ? route('products.index') : url('/produk') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/40 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/20">
                            Lihat CPNS Starter Kit
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
