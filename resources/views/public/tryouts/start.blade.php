@extends('layouts.public')

@section('title', 'Mulai ' . $tryoutPackage->tryout_type_label . ' - Ruang Cerdas')
@section('meta_description', 'Isi data peserta untuk mulai ' . $tryoutPackage->tryout_type_label . ' Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Mulai Tryout</p>
                <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">{{ $tryoutPackage->title }}</h1>
                <p class="mt-3 text-slate-600">
                    @if ($tryoutPackage->is_free)
                        Isi nama peserta terlebih dahulu. Email opsional agar sesi lebih mudah dikenali.
                    @else
                        Isi data peserta dan gunakan email pembelian agar akses tryout premium dapat diverifikasi.
                    @endif
                </p>

                @if (isset($recentPackageSession) && $recentPackageSession)
                    <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                        <div class="font-bold">Riwayat paket ini ditemukan di browser kamu.</div>
                        <div class="mt-1">
                            Status terakhir:
                            <span class="font-semibold">
                                {{ $recentPackageSession->isFinished() ? 'Selesai' : 'Belum selesai' }}
                            </span>
                            · mulai {{ $recentPackageSession->started_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ $recentPackageSession->isFinished() ? route('public.tryout-sessions.result', $recentPackageSession) : route('public.tryout-sessions.exam', $recentPackageSession) }}"
                               class="rc-btn-secondary px-4 py-2 text-sm">
                                {{ $recentPackageSession->isFinished() ? 'Lihat Hasil Terakhir' : 'Lanjutkan Sesi' }}
                            </a>
                            <a href="{{ route('public.tryout-sessions.history') }}"
                               class="rc-btn-neutral px-4 py-2 text-sm">
                                Lihat Riwayat
                            </a>
                        </div>
                    </div>
                @endif

                @if (! $tryoutPackage->is_free)
                    <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm text-blue-900">
                        Paket ini termasuk tryout premium. Akses hanya terbuka untuk email yang sudah membeli paket.
                    </div>
                @endif

                <form method="POST" action="{{ route('public.tryouts.packages.begin', ['tryoutType' => $tryoutPackage->routeSegment(), 'tryoutPackage' => $tryoutPackage]) }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="participant_name" class="mb-2 block text-sm font-semibold text-slate-700">Nama Peserta</label>
                        <input id="participant_name" name="participant_name" type="text" required value="{{ old('participant_name') }}"
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 outline-none ring-blue-200 focus:ring @error('participant_name') border-red-400 @enderror"
                               placeholder="Contoh: Budi Santoso">
                        @error('participant_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="participant_email" class="mb-2 block text-sm font-semibold text-slate-700">
                            Email Peserta
                            <span class="text-slate-400">{{ $tryoutPackage->is_free ? '(Opsional)' : '(Wajib untuk verifikasi akses)' }}</span>
                        </label>
                        <input id="participant_email" name="participant_email" type="email" value="{{ old('participant_email', $rememberedAccessEmail ?? '') }}" {{ $tryoutPackage->is_free ? '' : 'required' }}
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 outline-none ring-blue-200 focus:ring @error('participant_email') border-red-400 @enderror"
                               placeholder="nama@email.com">
                        @error('participant_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if (session('error'))
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-900">
                        Setelah menekan tombol mulai, sesi tryout langsung dibuat dan timer akan berjalan.
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <button type="submit" class="rc-btn-success w-full px-5 py-3.5 text-sm">
                            Mulai Sekarang
                        </button>
                        @if (! $tryoutPackage->is_free)
                            <a href="{{ route('public.tryouts.packages.buy', ['tryoutType' => $tryoutPackage->routeSegment(), 'tryoutPackage' => $tryoutPackage]) }}" class="rc-btn-primary w-full px-5 py-3.5 text-sm">
                                Beli Paket
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-slate-950">Ringkasan Paket</h2>
                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                            <span class="text-slate-600">Harga</span>
                            <span class="font-black text-slate-950">{{ $tryoutPackage->is_free ? 'Gratis' : 'Rp ' . number_format($tryoutPackage->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                            <span class="text-slate-600">Durasi</span>
                            <span class="font-black text-slate-950">{{ $tryoutPackage->duration_minutes }} menit</span>
                        </div>
                        @foreach ($tryoutPackage->sectionSummaries() as $section)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                                <span class="text-slate-600">{{ $section['label'] }}</span>
                                <span class="font-black text-slate-950">{{ $section['count'] }} soal</span>
                            </div>
                        @endforeach
                        @if (! $tryoutPackage->is_free)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                                <span class="text-slate-600">Percobaan</span>
                                <span class="font-black text-slate-950">{{ $tryoutPackage->attempt_limit ?: 1 }}x</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                                <span class="text-slate-600">Masa akses</span>
                                <span class="font-black text-slate-950">{{ $tryoutPackage->access_days ? $tryoutPackage->access_days . ' hari' : 'Tanpa batas' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-slate-950">Catatan</h2>
                    <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                        <li>- Paket harus aktif untuk bisa dimulai.</li>
                        <li>- Soal diambil acak dari bank soal aktif sesuai section paket.</li>
                        <li>- Saat waktu habis, jawaban akan otomatis disubmit.</li>
                        <li>- Jika sesi sebelumnya belum selesai di browser ini, sistem akan mencoba melanjutkan sesi yang sama.</li>
                        @if (! $tryoutPackage->is_free)
                            <li>- Percobaan paket premium akan berkurang saat sesi baru berhasil dibuat.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
