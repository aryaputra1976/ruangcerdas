@extends('layouts.public')

@section('title', 'Instruksi Pembayaran - ' . $order->invoice_number)
@section('robots', 'noindex,nofollow')

@section('content')
@php
    $bankAccounts = collect($paymentConfig['bank_accounts'] ?? [])
        ->map(function ($row, $index) {
            return [
                'bank_name' => trim((string) data_get($row, 'bank_name', '')),
                'account_number' => trim((string) data_get($row, 'account_number', '')),
                'account_holder' => trim((string) data_get($row, 'account_holder', '')),
                'is_primary' => (bool) data_get($row, 'is_primary', $index === 0),
            ];
        })
        ->filter(fn ($row) => $row['bank_name'] !== '' && $row['account_number'] !== '' && $row['account_holder'] !== '')
        ->values();
    $qrisImage = $paymentConfig['qris_image_path'] ?? ($paymentConfig['qris_image'] ?? null);
    $paymentNote = $paymentConfig['payment_note'] ?? 'Transfer sesuai nominal invoice agar verifikasi lebih cepat.';
    $qrisExists = $qrisImage && file_exists(public_path($qrisImage));
    $hasBankInstruction = $bankAccounts->isNotEmpty();
    $hasPaymentInstruction = $hasBankInstruction || $qrisExists;
    $isTryoutOrder = isset($tryoutPackage) && $tryoutPackage;

    $statusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Menunggu Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi Admin',
        \App\Models\Order::STATUS_PAID => 'Pembayaran Disetujui',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran Ditolak',
        default => str_replace('_', ' ', (string) $order->status),
    };

    $statusClass = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'bg-amber-100 text-amber-700',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'bg-blue-100 text-blue-700',
        \App\Models\Order::STATUS_PAID => 'bg-emerald-100 text-emerald-700',
        \App\Models\Order::STATUS_REJECTED => 'bg-red-100 text-red-700',
        default => 'bg-slate-100 text-slate-700',
    };

    $pageTitle = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Instruksi Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti Pembayaran Menunggu Verifikasi',
        \App\Models\Order::STATUS_PAID => 'Pembayaran Disetujui',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran Ditolak',
        default => 'Status Pembayaran',
    };

    $pageSubtitle = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Silakan transfer sesuai nominal, lalu upload bukti pembayaran untuk verifikasi admin.',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti pembayaran sudah diterima. Tim admin akan melakukan verifikasi.',
        \App\Models\Order::STATUS_PAID => $isTryoutOrder
            ? 'Pembayaran Anda sudah disetujui. Akses tryout premium aktif untuk email pembelian ini.'
            : 'Pembayaran Anda sudah disetujui. Silakan cek email untuk link download.',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran ditolak. Silakan periksa alasan dan upload ulang bukti jika diperlukan.',
        default => 'Silakan cek status order Anda secara berkala.',
    };

    $waUrl = \App\Support\WhatsApp::waMeUrl(
        $supportWhatsapp ?? null,
        'Halo Admin Ruang Cerdas, saya butuh bantuan untuk invoice ' . $order->invoice_number . '.'
    );
@endphp

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-8">
            <p class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">Pembayaran</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">{{ $pageTitle }}</h1>
            <p class="mt-3 max-w-3xl text-slate-600">{{ $pageSubtitle }}</p>
        </div>

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-center text-sm font-semibold text-slate-700">
            @if ($isTryoutOrder)
                Pembayaran manual · Verifikasi admin · Akses tryout aktif · Mulai dengan email pembelian
            @else
                Pembayaran manual · Verifikasi admin · Link download via email · Token aman
            @endif
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Invoice</p>
                            <h2 class="mt-2 break-all text-2xl font-black text-slate-950 md:text-3xl">{{ $order->invoice_number }}</h2>
                        </div>
                        <span class="inline-flex rounded-full px-4 py-2 text-sm font-bold uppercase {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Nama Produk</p>
                            <p class="mt-1 font-black text-slate-950">{{ $order->product->name }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Email Pembeli</p>
                            <p class="mt-1 break-all font-black text-slate-950">{{ $order->buyer_email }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Metode Pembayaran</p>
                            <p class="mt-1 font-black text-slate-950">{{ $hasBankInstruction ? 'Transfer Bank' : ($qrisExists ? 'QRIS' : '-') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Total Bayar</p>
                            <p class="mt-1 text-2xl font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</p>
                        </div>
                    </div>

                    @if ($hasBankInstruction)
                        <div class="mt-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm text-slate-500">Rekening Tujuan</p>
                                @if ($bankAccounts->count() > 1)
                                    <p class="text-xs font-semibold text-slate-500">Pilih salah satu rekening di bawah untuk transfer manual.</p>
                                @endif
                            </div>
                            <div class="mt-2 grid gap-3 md:grid-cols-2">
                                @foreach ($bankAccounts as $account)
                                    <div class="rounded-2xl border {{ $account['is_primary'] ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-slate-50' }} p-4">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="font-black text-slate-950">{{ $account['bank_name'] }}</p>
                                            @if ($account['is_primary'])
                                                <span class="rounded-full bg-blue-100 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-blue-700">Utama</span>
                                            @endif
                                        </div>
                                        <p class="mt-2 break-all text-xl font-black tracking-wide text-slate-950">{{ $account['account_number'] }}</p>
                                        <p class="mt-1 text-sm text-slate-600">a.n. {{ $account['account_holder'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-4 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800">
                            Instruksi pembayaran belum tersedia. Silakan hubungi admin Ruang Cerdas.
                        </div>
                    @endif

                    @if ($hasPaymentInstruction && !empty($paymentNote))
                        <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-900">
                            {{ $paymentNote }}
                        </div>
                    @endif

                    @if ($order->status !== \App\Models\Order::STATUS_PAID && ! $order->payment_proof_path)
                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                            <h3 class="text-lg font-black text-amber-950">Sudah transfer?</h3>
                            <p class="mt-2 text-sm leading-6 text-amber-800">
                                Setelah transfer, upload bukti pembayaran agar admin bisa segera memverifikasi order Anda.
                            </p>
                            <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700 sm:w-auto">
                                Saya Sudah Bayar, Upload Bukti Pembayaran
                            </a>
                        </div>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                    <h2 class="text-2xl font-black text-slate-950">Langkah Selanjutnya</h2>
                    <div class="mt-6 grid gap-3">
                        @foreach (($isTryoutOrder
                            ? [
                                'Transfer sesuai nominal.',
                                'Upload bukti pembayaran.',
                                'Tunggu verifikasi admin.',
                                'Setelah approved, buka halaman tryout dan gunakan email pembelian untuk mulai.',
                            ]
                            : [
                                'Transfer sesuai nominal.',
                                'Upload bukti pembayaran.',
                                'Tunggu verifikasi admin.',
                                'Link download dikirim ke email.',
                            ]) as $index => $step)
                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">{{ $index + 1 }}</span>
                                <span>{{ $step }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($order->status === \App\Models\Order::STATUS_PENDING)
                        <div class="mt-5 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800">
                            Order masih menunggu pembayaran. Setelah transfer selesai, lanjutkan ke upload bukti pembayaran agar admin bisa memverifikasi.
                        </div>
                    @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                        <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-800">
                            Bukti pembayaran sudah diterima. Saat ini order Anda sedang menunggu verifikasi admin.
                        </div>
                    @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                        <div class="mt-5 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4 text-sm leading-6 text-emerald-800">
                            @if ($isTryoutOrder)
                                Pembayaran sudah disetujui. Akses tryout premium aktif. Silakan buka halaman tryout dan gunakan email pembelian Anda.
                            @else
                                Pembayaran sudah disetujui. Silakan cek inbox dan folder spam email Anda untuk link download.
                            @endif
                        </div>
                    @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                        <div class="mt-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-4 text-sm leading-6 text-red-800">
                            Pembayaran ditolak. Silakan periksa alasannya di bawah dan upload ulang bukti jika diperlukan.
                        </div>
                    @endif

                    @if ($order->status === \App\Models\Order::STATUS_REJECTED && !empty($order->rejection_reason))
                        <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm leading-6 text-red-700">
                            Alasan penolakan: {{ $order->rejection_reason }}
                        </div>
                    @endif

                    @if ($isTryoutOrder)
                        <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 p-5">
                            <h3 class="text-lg font-black text-blue-950">Cara membuka tryout premium</h3>
                            <ul class="mt-3 space-y-2 text-sm leading-6 text-blue-900">
                                <li>- Buka halaman Tryout CPNS setelah pembayaran disetujui.</li>
                                <li>- Pilih paket <span class="font-bold">{{ $tryoutPackage->title }}</span>.</li>
                                <li>- Klik <span class="font-bold">Mulai Tryout</span>.</li>
                                <li>- Gunakan email pembelian: <span class="font-bold">{{ $order->buyer_email }}</span>.</li>
                            </ul>
                            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ route('public.tryouts.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                                    Buka Halaman Tryout
                                </a>
                                <a href="{{ route('public.tryouts.start', $tryoutPackage) }}" class="inline-flex items-center justify-center rounded-2xl border border-blue-200 bg-white px-5 py-3 text-sm font-bold text-blue-700 hover:bg-blue-50">
                                    Mulai Paket Ini
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div id="upload-bukti" class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8 scroll-mt-24">
                    <h2 class="text-2xl font-black text-slate-950">Upload Bukti Pembayaran</h2>

                    @if ($order->payment_proof_path)
                        <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-semibold text-blue-700">
                            Bukti pembayaran sudah diupload.
                            @if ($order->payment_uploaded_at)
                                <div class="mt-1 font-normal text-blue-600">Tanggal upload: {{ $order->payment_uploaded_at->format('d M Y H:i') }}</div>
                            @endif
                        </div>
                        @if ($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                                Kirim Pemberitahuan ke Admin WhatsApp
                            </a>
                        @else
                            <p class="mt-4 text-sm text-slate-600">Jika perlu bantuan, silakan hubungi admin Ruang Cerdas.</p>
                        @endif
                    @elseif ($order->status !== \App\Models\Order::STATUS_PAID)
                        <p class="mt-4 text-sm leading-6 text-slate-600">
                            Setelah transfer selesai, upload bukti pembayaran dalam format JPG, PNG, atau PDF agar admin mudah memverifikasi.
                        </p>
                        <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">
                            Saya Sudah Bayar, Upload Bukti Pembayaran
                        </a>
                    @endif

                    @if ($order->status === \App\Models\Order::STATUS_PAID && ! $isTryoutOrder && filled($order->download_token))
                        <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-800">
                            Pembayaran Anda sudah disetujui. Anda bisa langsung mengunduh file digital melalui tombol di bawah.
                        </div>
                        <a href="{{ route('orders.download', ['invoice' => $order->invoice_number, 'token' => $order->download_token]) }}" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-700">
                            Download File Digital
                        </a>
                    @elseif ($order->status === \App\Models\Order::STATUS_PAID && $isTryoutOrder)
                        <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-800">
                            Akses tryout premium Anda sudah aktif. Gunakan email pembelian ini saat mulai tryout.
                        </div>
                    @endif
                </div>

                @if ($waUrl)
                    <div class="rounded-[2rem] border border-green-200 bg-green-50 p-6 shadow-sm md:p-8">
                        <h2 class="text-2xl font-black text-green-900">Butuh Bantuan Pembelian?</h2>
                        <p class="mt-3 text-sm leading-7 text-green-800">
                            Anda tetap dapat menyelesaikan pembelian secara mandiri. Jika ada kendala, tim support siap membantu melalui WhatsApp.
                        </p>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-bold text-white hover:bg-green-700">
                            WhatsApp Support
                        </a>
                    </div>
                @endif
            </div>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ringkasan Invoice</p>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Invoice</span><span class="font-bold text-slate-950 break-all text-right">{{ $order->invoice_number }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Nama</span><span class="font-bold text-slate-950">{{ $order->buyer_name }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Email</span><span class="font-bold text-slate-950 break-all text-right">{{ $order->buyer_email }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">WhatsApp</span><span class="font-bold text-slate-950">{{ $order->buyer_whatsapp }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Produk</span><span class="font-bold text-slate-950 text-right">{{ $order->product->name }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Status</span><span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">{{ $statusLabel }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Tanggal Order</span><span class="font-bold text-slate-950">{{ $order->created_at?->format('d M Y H:i') }}</span></div>
                    </div>

                    <div class="mt-5 border-t border-slate-200 pt-4">
                        @if ((float) ($order->discount_amount ?? 0) > 0)
                            <div class="mb-2 flex justify-between gap-3 text-sm"><span class="text-slate-500">Harga Awal</span><span class="font-bold text-slate-700">{{ \App\Support\Money::rupiah((float) ($order->original_price ?? $order->price)) }}</span></div>
                            <div class="mb-2 flex justify-between gap-3 text-sm"><span class="text-slate-500">Kode Kupon</span><span class="font-bold text-emerald-700">{{ $order->coupon_code ?: '-' }}</span></div>
                            <div class="mb-2 flex justify-between gap-3 text-sm"><span class="text-slate-500">Diskon</span><span class="font-bold text-emerald-700">-{{ \App\Support\Money::rupiah((float) $order->discount_amount) }}</span></div>
                        @endif
                        <div class="flex justify-between gap-3"><span class="text-slate-600">Total Bayar</span><span class="text-2xl font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</span></div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50 p-6 shadow-sm">
                    <h3 class="text-lg font-black text-emerald-950">QRIS</h3>
                    <div class="mt-4 rounded-2xl bg-white p-4 text-center">
                        @if ($qrisExists)
                            <img src="{{ asset($qrisImage) }}" alt="QRIS Ruang Cerdas" class="mx-auto w-full max-w-[240px] rounded-2xl border border-slate-200">
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-600">QRIS belum tersedia.</div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('public.order-tracking.index') }}" class="flex-1 rounded-2xl bg-blue-600 px-6 py-4 text-center font-bold text-white hover:bg-blue-700">
                Cek Status Order
            </a>
            <a href="{{ route('products.index') }}" class="flex-1 rounded-2xl border border-slate-300 bg-white px-6 py-4 text-center font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                Kembali ke Produk
            </a>
            @if ($order->status === \App\Models\Order::STATUS_PAID)
                <span class="flex-1 rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-center font-semibold text-emerald-700">
                    {{ $isTryoutOrder ? 'Akses Tryout Sudah Aktif' : 'Pembayaran Sudah Disetujui' }}
                </span>
            @endif
        </div>
    </div>
</section>
@endsection
