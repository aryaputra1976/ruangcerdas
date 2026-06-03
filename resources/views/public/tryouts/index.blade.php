@extends('layouts.public')

@section('title', 'Tryout CPNS - Ruang Cerdas')
@section('meta_description', 'Daftar paket tryout CPNS online Ruang Cerdas untuk latihan TWK, TIU, dan TKP.')
@section('canonical', route('public.tryouts.index'))

@section('content')
<section class="relative overflow-hidden bg-slate-950 py-16 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.35),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.28),_transparent_32%)]"></div>

    <div class="relative mx-auto max-w-7xl px-6">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="inline-flex rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-widest text-blue-100">
                    Tryout CPNS
                </p>
                <h1 class="mt-5 text-4xl font-black tracking-tight md:text-5xl">Pilih Paket Tryout CPNS Online</h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-slate-300 md:text-lg">
                    Fondasi latihan TWK, TIU, dan TKP dari Ruang Cerdas. Pilih paket aktif yang paling sesuai untuk target belajarmu.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#paket-tryout" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700">
                        Lihat Paket
                    </a>
                    <a href="{{ route('public.tryout-sessions.history') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                        Riwayat Tryout
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-white/10 p-6 shadow-2xl backdrop-blur">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-white p-5 text-slate-950">
                        <div class="text-3xl font-black text-blue-600">TWK</div>
                        <p class="mt-4 text-sm font-bold text-slate-500">Wawasan</p>
                    </div>
                    <div class="rounded-3xl bg-white p-5 text-slate-950">
                        <div class="text-3xl font-black text-blue-600">TIU</div>
                        <p class="mt-4 text-sm font-bold text-slate-500">Intelegensi</p>
                    </div>
                    <div class="rounded-3xl bg-white p-5 text-slate-950">
                        <div class="text-3xl font-black text-blue-600">TKP</div>
                        <p class="mt-4 text-sm font-bold text-slate-500">Karakteristik</p>
                    </div>
                </div>
                <div class="mt-4 rounded-3xl bg-white/10 p-5 text-sm leading-7 text-slate-200">
                    Tryout gratis tetap bisa langsung dicoba, sedangkan paket premium memberi soal lebih lengkap, pembahasan, dan percobaan lebih banyak.
                </div>
            </div>
        </div>
    </div>
</section>

<section id="paket-tryout" class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Paket Aktif</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Daftar Paket Tryout</h2>
                <p class="mt-3 max-w-2xl text-slate-600">Pilih paket gratis untuk pemanasan, atau lanjut ke premium jika ingin simulasi yang lebih lengkap dan terarah.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 shadow-sm">
                Total: <span class="font-black text-slate-950">{{ number_format($packages->count(), 0, ',', '.') }}</span> paket
            </div>
        </div>

        @if (session('error'))
            <div class="mb-8 rounded-[2rem] border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($hasTryoutHistory ?? false)
            <div class="mb-8 rounded-[2rem] border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                Kamu punya riwayat sesi tryout di browser ini.
                <a href="{{ route('public.tryout-sessions.history') }}" class="ml-1 font-bold text-blue-700 hover:text-blue-800">
                    Lihat riwayat tryout
                </a>
            </div>
        @endif

        @if ($packages->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($packages as $package)
                    @php
                        $packageState = $packageStates[$package->id] ?? ['canStart' => $package->is_free, 'hasAccess' => false, 'buyUrl' => route('public.tryouts.buy', $package)];
                        $isPremium = ! $package->is_free;
                        $defaultDescription = $package->is_free
                            ? 'Coba simulasi dasar sebelum membeli paket lengkap.'
                            : ($package->slug === 'tryout-premium-mini'
                                ? 'Latihan lebih banyak dengan pembahasan singkat.'
                                : 'Simulasi SKD lebih serius dengan komposisi soal lengkap.');
                    @endphp
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $package->is_free ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $package->is_free ? 'Gratis' : 'Premium' }}
                                </span>
                                <h3 class="mt-4 text-2xl font-black text-slate-950">{{ $package->title }}</h3>
                            </div>
                            <div class="rounded-2xl {{ $package->is_free ? 'bg-emerald-600' : 'bg-slate-900' }} px-4 py-2 text-sm font-bold text-white">
                                {{ $package->is_free ? 'Gratis' : 'Rp ' . number_format($package->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $package->description ?: $defaultDescription }}</p>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Durasi</div>
                                <div class="mt-2 text-lg font-black text-slate-950">{{ $package->duration_minutes }} menit</div>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Soal</div>
                                <div class="mt-2 text-lg font-black text-slate-950">{{ $package->total_questions }}</div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                <span class="font-semibold text-slate-600">TWK</span>
                                <span class="font-black text-slate-950">{{ $package->twk_count }} soal</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                <span class="font-semibold text-slate-600">TIU</span>
                                <span class="font-black text-slate-950">{{ $package->tiu_count }} soal</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                <span class="font-semibold text-slate-600">TKP</span>
                                <span class="font-black text-slate-950">{{ $package->tkp_count }} soal</span>
                            </div>
                        </div>

                        @if ($isPremium)
                            <div class="mt-4 space-y-2 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
                                <div class="flex items-center justify-between">
                                    <span>Pembahasan</span>
                                    <span class="font-bold">{{ $package->has_explanation ? 'Tersedia' : 'Ringkas' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Jumlah percobaan</span>
                                    <span class="font-bold">{{ $package->attempt_limit ?: 1 }}x</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Masa akses</span>
                                    <span class="font-bold">{{ $package->access_days ? $package->access_days . ' hari' : 'Tanpa batas' }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="mt-6">
                            @if ($packageState['canStart'])
                                <a href="{{ route('public.tryouts.start', $package) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                                    Mulai Tryout
                                </a>
                            @else
                                <a href="{{ $packageState['buyUrl'] }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/10 transition hover:bg-blue-700">
                                    Beli Tryout
                                </a>
                            @endif
                        </div>

                        @if ($isPremium && ! $packageState['canStart'])
                            <p class="mt-3 text-xs leading-6 text-slate-500">
                                Paket premium hanya bisa dimulai setelah Anda memiliki akses aktif.
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-xl font-black text-slate-700">RC</div>
                <h2 class="mt-5 text-2xl font-black text-slate-950">Paket tryout belum tersedia</h2>
                <p class="mx-auto mt-3 max-w-md text-slate-600">Admin belum menayangkan paket tryout aktif. Silakan cek lagi nanti.</p>
            </div>
        @endif
    </div>
</section>
@endsection
