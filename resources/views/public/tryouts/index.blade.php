@extends('layouts.public')

@section('title', $pageHeading . ' - Ruang Cerdas')
@section('meta_description', $pageMetaDescription)
@section('canonical', route($pageRouteName))

@section('content')
<section id="paket-tryout" class="bg-slate-50 pt-3 pb-7 md:pt-4 md:pb-9">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-5 flex flex-col gap-4 md:mb-6 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">{{ $pageTitle }}</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:mt-4 md:text-4xl">{{ $pageHeading }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600 md:mt-3 md:text-base">{{ $pageDescription }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $pageHistoryUrl }}" class="rc-btn-neutral px-5 py-3 text-sm shadow-sm">
                    Riwayat Tryout
                </a>
                <a href="{{ $pageBackUrl }}" class="rc-btn-neutral px-5 py-3 text-sm shadow-sm">
                    Kembali
                </a>
                <div class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 shadow-sm">
                    Total: <span class="ml-1 font-black text-slate-950">{{ number_format($packages->count(), 0, ',', '.') }}</span> paket
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="mb-6 rounded-[2rem] border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($hasTryoutHistory ?? false)
            <div class="mb-6 rounded-[2rem] border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                Kamu punya riwayat sesi tryout di browser ini.
                <a href="{{ route('public.tryout-sessions.history') }}" class="ml-1 font-bold text-blue-700 hover:text-blue-800">
                    Lihat riwayat tryout
                </a>
            </div>
        @endif

        @if (($packages->first()?->tryout_type ?? null) === \App\Support\TryoutBlueprint::TYPE_CPNS)
            <div class="mb-6 rounded-[2rem] border border-slate-200 bg-white px-5 py-4 text-sm leading-7 text-slate-600 shadow-sm">
                Paket CPNS mini memakai <span class="font-bold text-slate-950">ambang paket latihan</span> yang disesuaikan dengan jumlah soal.
                Paket SKD lengkap memakai <span class="font-bold text-slate-950">ambang simulasi SKD penuh</span>.
            </div>
        @endif

        @if ($packages->isNotEmpty())
            <div class="grid items-stretch gap-4 md:gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($packages as $package)
                    @php
                        $isFreePackage = $package->isFreePackage();
                        $packageState = $packageStates[$package->id] ?? ['canStart' => $isFreePackage, 'hasAccess' => false, 'buyUrl' => '#', 'startUrl' => '#'];
                        $isPremium = ! $isFreePackage;
                        $defaultDescription = $isFreePackage ? 'Coba simulasi dasar sebelum membeli paket lengkap.' : 'Simulasi yang lebih serius dengan komposisi soal lengkap.';
                        $displayDescription = \Illuminate\Support\Str::limit($package->description ?: $defaultDescription, 170);
                        $sectionSummaries = collect($package->sectionSummaries());
                        $sectionCount = $sectionSummaries->count();
                        $displayTitle = $package->cardDisplayTitle();
                        $displaySubtitle = $package->cardDisplaySubtitle();
                        $sectionChipLabel = $sectionCount === 1
                            ? ($sectionSummaries->first()['label'] ?? '1 section')
                            : $sectionCount . ' section';
                        $cardClass = $isFreePackage
                            ? 'border-slate-200 bg-white'
                            : 'border-blue-200 bg-gradient-to-br from-white via-white to-blue-50/70 shadow-blue-100/60';
                        $badgeClass = $isFreePackage
                            ? 'bg-slate-100 text-slate-700'
                            : 'border border-amber-300 bg-amber-50 text-amber-800';
                        $priceClass = $isFreePackage
                            ? 'border border-slate-200 bg-slate-50 text-slate-700'
                            : 'bg-slate-800 text-white';
                        $statClass = $isFreePackage
                            ? 'bg-slate-50'
                            : 'bg-slate-50';
                    @endphp
                    <article class="flex h-full flex-col rounded-[2rem] border p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl md:p-5 {{ $cardClass }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $isFreePackage ? 'Gratis' : 'Premium' }}
                                </span>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-2">
                                @if ($isPremium)
                                    <span class="rounded-full bg-red-500 px-3 py-1 text-xs font-bold tracking-wide text-white shadow-sm">
                                        Update 2026
                                    </span>
                                @endif
                                    <div class="rounded-[1.1rem] px-4 py-2 text-base font-bold whitespace-nowrap md:px-4 md:py-2.5 {{ $priceClass }}">
                                    @if ($isFreePackage)
                                        Gratis
                                    @else
                                        Rp {{ number_format($package->price, 0, ',', '.') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 min-h-[4.2rem] md:min-h-[5.2rem]">
                            @if ($displaySubtitle && $displaySubtitle !== $displayTitle)
                                <p class="mb-1 line-clamp-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $displaySubtitle }}</p>
                            @endif
                            <h3 class="line-clamp-3 text-[1.28rem] font-black leading-tight tracking-tight text-slate-950 md:text-[1.55rem]" title="{{ $package->title }}">{{ $displayTitle }}</h3>
                        </div>

                        <div class="mt-2 min-h-[3.9rem] md:mt-3 md:min-h-[4.4rem]">
                            <p class="line-clamp-3 text-sm leading-6 text-slate-600">{{ $displayDescription }}</p>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 md:mt-4">
                            <div class="rounded-2xl p-4 {{ $statClass }}">
                                <div class="text-xs font-medium capitalize tracking-normal text-slate-600">Durasi</div>
                                <div class="mt-1.5 text-lg font-black text-slate-950">{{ $package->duration_minutes }} menit</div>
                            </div>
                            <div class="rounded-2xl p-4 {{ $statClass }}">
                                <div class="text-xs font-medium capitalize tracking-normal text-slate-600">Total soal</div>
                                <div class="mt-1.5 text-lg font-black text-slate-950">{{ $package->total_questions }}</div>
                            </div>
                        </div>

                        <div class="mt-3 flex min-h-[2.6rem] flex-wrap content-start gap-2 md:mt-4">
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-900">
                                {{ $sectionChipLabel }}
                            </span>
                            @if ($package->has_explanation)
                                <span class="inline-flex rounded-full bg-sky-100 px-3 py-1.5 text-xs font-medium text-sky-800">
                                    Ada pembahasan
                                </span>
                            @endif
                            @if ($isPremium)
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-900">
                                    {{ $package->attempt_limit ?: 1 }}x percobaan
                                </span>
                            @endif
                        </div>

                        <div class="mt-auto pt-4">
                            @if ($packageState['canStart'])
                                <a href="{{ $packageState['startUrl'] }}" class="{{ $isFreePackage ? 'rc-btn-secondary' : 'rc-btn-success' }} w-full px-5 py-3 text-sm">
                                    {{ $isFreePackage ? 'Coba Gratis Sekarang' : 'Mulai Tryout' }}
                                </a>
                            @else
                                <a href="{{ $packageState['buyUrl'] }}" class="rc-btn-primary w-full px-5 py-3 text-sm">
                                    Beli Tryout
                                </a>
                            @endif
                        </div>

                        @if ($isPremium)
                            <p class="mt-3 min-h-[2.5rem] text-center text-xs leading-5 text-slate-500">
                                {{ $package->access_days ? 'Akses ' . $package->access_days . ' hari' : 'Akses tanpa batas hari' }}, {{ $package->attempt_limit ?: 1 }}x percobaan.
                                @if (! $packageState['canStart'])
                                    Beli dulu, lalu mulai setelah akses aktif.
                                @endif
                            </p>
                        @else
                            <p class="mt-3 min-h-[2.5rem] text-xs leading-5 text-slate-500">
                                Bisa langsung dicoba tanpa beli.
                            </p>
                        @endif

                        @if ($package->tryout_type === \App\Support\TryoutBlueprint::TYPE_CPNS && $package->thresholdSummaryLine())
                            <div class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">
                                    {{ $package->thresholdLabel() }}
                                </p>
                                <p class="mt-1 text-xs leading-6 text-slate-700">
                                    {{ $package->thresholdSummaryLine() }}
                                </p>
                            </div>
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
