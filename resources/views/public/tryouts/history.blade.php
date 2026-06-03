@extends('layouts.public')

@section('title', 'Riwayat Tryout CPNS - Ruang Cerdas')
@section('meta_description', 'Riwayat sesi tryout CPNS pada browser ini.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Riwayat Tryout</p>
                <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Sesi Tryout di Browser Ini</h1>
                <p class="mt-3 max-w-2xl text-slate-600">Riwayat ini tersimpan di browser yang sama, tanpa login. Kamu bisa lanjutkan sesi yang belum selesai atau lihat hasil sesi yang sudah selesai.</p>
            </div>
            <a href="{{ route('public.tryouts.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">
                Kembali ke Paket
            </a>
        </div>

        @if ($sessions->isNotEmpty())
            <div class="grid gap-5">
                @foreach ($sessions as $session)
                    @php
                        $isFinished = $session->isFinished();
                    @endphp
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700">
                                        {{ $session->package?->title ?? 'Tryout CPNS' }}
                                    </span>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $isFinished ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $isFinished ? 'Selesai' : 'Belum Selesai' }}
                                    </span>
                                </div>
                                <h2 class="mt-4 text-2xl font-black text-slate-950">{{ $session->participant_name }}</h2>
                                <p class="mt-2 text-sm text-slate-600">{{ $session->participant_email ?: 'Email tidak diisi' }}</p>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                                        <div class="text-slate-500">Mulai</div>
                                        <div class="mt-1 font-bold text-slate-950">{{ $session->started_at?->format('d M Y H:i') ?? '-' }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                                        <div class="text-slate-500">Durasi</div>
                                        <div class="mt-1 font-bold text-slate-950">{{ $session->duration_minutes }} menit</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                                        <div class="text-slate-500">Status</div>
                                        <div class="mt-1 font-bold text-slate-950">{{ ucfirst($session->status) }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                                        <div class="text-slate-500">Skor Total</div>
                                        <div class="mt-1 font-bold text-slate-950">{{ $isFinished ? $session->total_score : '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @if ($isFinished)
                                    <a href="{{ route('public.tryout-sessions.result', $session) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                                        Lihat Hasil
                                    </a>
                                    <a href="{{ route('public.tryout-sessions.review', $session) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:border-blue-300 hover:text-blue-700">
                                        Pembahasan
                                    </a>
                                @else
                                    <a href="{{ route('public.tryout-sessions.exam', $session) }}" class="inline-flex items-center justify-center rounded-2xl bg-amber-500 px-5 py-3 text-sm font-bold text-white hover:bg-amber-600">
                                        Lanjutkan Sesi
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-xl font-black text-slate-700">RC</div>
                <h2 class="mt-5 text-2xl font-black text-slate-950">Belum ada riwayat tryout</h2>
                <p class="mx-auto mt-3 max-w-md text-slate-600">Riwayat sesi akan muncul setelah kamu memulai tryout di browser ini.</p>
                <div class="mt-6">
                    <a href="{{ route('public.tryouts.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">
                        Mulai Tryout
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
