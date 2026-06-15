@extends('layouts.public')

@section('title', 'Hasil ' . ($tryoutSession->package?->tryout_type_label ?? 'Tryout') . ' - Ruang Cerdas')
@section('meta_description', 'Hasil tryout Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@section('content')
@php
    $tryoutType = $tryoutSession->package?->tryout_type;
    $isPppkStyleResult = in_array($tryoutType, [\App\Support\TryoutBlueprint::TYPE_PPPK, \App\Support\TryoutBlueprint::TYPE_PPPK_TENDIK], true);
    $thresholdLabel = $tryoutSession->package?->thresholdLabel() ?? 'Ambang paket';
    $usesScaledCpnsThresholds = $tryoutSession->package?->usesScaledCpnsThresholds() ?? false;
    $isFreePackage = $tryoutSession->package?->isFreePackage() ?? false;
    $displayTitle = $tryoutSession->package?->cardDisplayTitle() ?? ($tryoutSession->package?->title ?? 'Tryout');
    $displaySubtitle = $tryoutSession->package?->cardDisplaySubtitle();
@endphp
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Hasil Tryout</p>
                    <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">{{ $tryoutSession->participant_name }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $isFreePackage ? 'bg-slate-100 text-slate-700' : 'border border-amber-300 bg-amber-50 text-amber-800' }}">
                            {{ $isFreePackage ? 'Paket Gratis' : 'Paket Premium' }}
                        </span>
                        @if ($isPppkStyleResult)
                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700">
                                Simulasi Latihan
                            </span>
                        @endif
                    </div>
                    @if ($displaySubtitle && $displaySubtitle !== $displayTitle)
                        <p class="mt-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ $displaySubtitle }}</p>
                    @endif
                    <p class="mt-1 text-slate-600" title="{{ $tryoutSession->package?->title ?? 'Tryout' }}">{{ $displayTitle }}</p>
                </div>
                <div class="rounded-3xl px-5 py-4 text-center {{ $isPppkStyleResult ? 'bg-slate-100 text-slate-700' : ($isPassed === null ? 'bg-slate-100 text-slate-700' : ($isPassed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700')) }}">
                    <div class="text-xs font-bold uppercase tracking-widest">
                        {{ $isPppkStyleResult ? 'Hasil Simulasi' : ($isPassed === null ? 'Tanpa Ambang Batas' : ($isPassed ? 'Lulus' : 'Belum Lulus')) }}
                    </div>
                    <div class="mt-2 text-2xl font-black">
                        {{ $isPppkStyleResult ? 'Skor Berhasil Dihitung' : ($isPassed === null ? 'Simulasi Selesai' : ($isPassed ? 'Lulus Simulasi' : 'Belum Lulus Simulasi')) }}
                    </div>
                    @if ($isPppkStyleResult)
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Seleksi PPPK dinilai dengan peringkat terbaik, bukan passing grade tetap.
                        </p>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($sectionResults as $section)
                    <div class="rounded-3xl bg-blue-50 p-5">
                        <div class="text-sm font-bold uppercase tracking-widest text-blue-700">{{ $section['label'] }}</div>
                        <div class="mt-3 text-3xl font-black text-slate-950">{{ $section['score'] }}</div>
                        <div class="mt-2 text-sm text-slate-600">
                            @if ($isPppkStyleResult)
                                {{ $section['question_count'] }} soal · hasil simulasi section
                            @else
                                {{ $section['threshold'] !== null ? $thresholdLabel . ' ' . $section['threshold'] : 'Tanpa nilai minimum' }}
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="rounded-3xl bg-slate-950 p-5 text-white">
                    <div class="text-sm font-bold uppercase tracking-widest text-slate-300">{{ $isPppkStyleResult ? 'Total Skor' : 'Total' }}</div>
                    <div class="mt-3 text-3xl font-black">{{ $tryoutSession->total_score }}</div>
                    <div class="mt-2 text-sm text-slate-300">
                        @if ($isPppkStyleResult)
                            Skor akumulasi dari seluruh jawaban
                        @else
                            {{ $totalThreshold !== null ? $thresholdLabel . ' ' . $totalThreshold : 'Tanpa nilai minimum' }}
                        @endif
                    </div>
                </div>
            </div>

            @if ($usesScaledCpnsThresholds)
                <div class="mt-5 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm leading-7 text-amber-900">
                    Hasil ini memakai <span class="font-bold">ambang paket latihan</span> yang disesuaikan dengan jumlah soal di paket ini, jadi patokannya berbeda dari passing grade SKD CPNS penuh.
                </div>
            @endif

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200 p-6">
                    <h2 class="text-xl font-black text-slate-950">Ringkasan Section</h2>
                    <div class="mt-5 grid gap-4">
                        @foreach ($sectionResults as $section)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="text-sm font-semibold text-slate-600">{{ $section['label'] }}</div>
                                <div class="mt-2 text-lg font-black text-slate-950">
                                    @if ($isPppkStyleResult)
                                        @php
                                            $sectionMaxScore = $section['question_count'] * \App\Support\TryoutBlueprint::maxWeightedScore($tryoutType, $section['key']);
                                        @endphp
                                        {{ $section['question_count'] }} soal · total skor {{ $section['score'] }} dari maksimum {{ $sectionMaxScore }}
                                    @elseif ($section['correct_count'] === null)
                                        {{ $section['question_count'] }} soal · skor bertingkat
                                    @else
                                        {{ $section['correct_count'] }} benar / {{ $section['incorrect_count'] }} salah
                                    @endif
                                </div>
                                @if ($isPppkStyleResult)
                                    <p class="mt-2 text-sm leading-6 text-slate-500">
                                        Rata-rata {{ number_format($section['question_count'] > 0 ? ($section['score'] / $section['question_count']) : 0, 2, ',', '.') }} poin per soal.
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 p-6">
                    @if ($isPppkStyleResult)
                        <h2 class="text-xl font-black text-slate-950">Cara Membaca Hasil</h2>
                        <div class="mt-5 space-y-3 text-sm leading-7 text-slate-600">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                Total skor Anda adalah <span class="font-bold text-slate-950">{{ $tryoutSession->total_score }}</span>, yaitu hasil penjumlahan seluruh bobot jawaban yang dipilih.
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                Untuk PPPK Tendik, hasil tryout dipakai sebagai <span class="font-bold text-slate-950">simulasi performa latihan</span>, bukan status lulus atau tidak lulus.
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                Seleksi resminya mengikuti <span class="font-bold text-slate-950">peringkat terbaik</span> sesuai formasi, prioritas pelamar, nilai CAT, dan tahapan seleksi lanjutan.
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                Gunakan skor per section untuk melihat area yang sudah kuat dan area yang masih perlu ditingkatkan.
                            </div>
                        </div>
                    @else
                        <h2 class="text-xl font-black text-slate-950">Status Kelulusan</h2>
                        <div class="mt-5 space-y-3 text-sm text-slate-600">
                            @foreach ($sectionResults as $section)
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span>{{ $section['label'] }}{{ $section['threshold'] !== null ? ' >= ' . $section['threshold'] : '' }}</span>
                                    <span class="font-bold {{ $section['threshold'] === null ? 'text-slate-600' : ($section['score'] >= $section['threshold'] ? 'text-emerald-600' : 'text-rose-600') }}">
                                        {{ $section['threshold'] === null ? 'Tidak dinilai ambang batas' : ($section['score'] >= $section['threshold'] ? 'Lolos' : 'Belum') }}
                                    </span>
                                </div>
                            @endforeach
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <span>Total{{ $totalThreshold !== null ? ' >= ' . $totalThreshold : '' }}</span>
                                <span class="font-bold {{ $totalThreshold === null ? 'text-slate-600' : ($tryoutSession->total_score >= $totalThreshold ? 'text-emerald-600' : 'text-rose-600') }}">
                                    {{ $totalThreshold === null ? 'Tidak dinilai ambang batas' : ($tryoutSession->total_score >= $totalThreshold ? 'Lolos' : 'Belum') }}
                                </span>
                            </div>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-slate-500">
                            Patokan di atas memakai {{ strtolower($thresholdLabel) }} untuk paket yang sedang Anda kerjakan.
                        </p>
                    @endif
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

            @if ($isFreePackage)
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
