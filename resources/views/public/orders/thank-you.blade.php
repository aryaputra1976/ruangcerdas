@extends('layouts.public')

@section('title', 'Instruksi Pembayaran - ' . $order->invoice_number)
@section('robots', 'noindex,nofollow')

@section('content')
@php
    $bankName = $paymentConfig['bank_name'] ?? 'Bank Mandiri';
    $bankAccountNumber = $paymentConfig['bank_account_number'] ?? '1234567890';
    $bankAccountHolder = $paymentConfig['bank_account_holder'] ?? 'Ruang Cerdas';
    $qrisImage = $paymentConfig['qris_image_path'] ?? ($paymentConfig['qris_image'] ?? null);
    $paymentNote = $paymentConfig['payment_note'] ?? 'Transfer sesuai nominal invoice agar verifikasi lebih cepat.';
    $qrisExists = $qrisImage && file_exists(public_path($qrisImage));

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
        \App\Models\Order::STATUS_PAID => 'Pembayaran Anda sudah disetujui. Silakan cek email untuk link download.',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran ditolak. Silakan periksa alasan dan upload ulang bukti jika diperlukan.',
        default => 'Silakan cek status order Anda secara berkala.',
    };

    $waAdmin = '6285182723065';
    $waMessage = rawurlencode('Halo Admin Ruang Cerdas, saya sudah upload bukti pembayaran untuk invoice ' . $order->invoice_number . '. Mohon dibantu cek. Terima kasih.');
    $waUrl = 'https://wa.me/' . $waAdmin . '?text=' . $waMessage;
@endphp

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-10">
            <p class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">Pembayaran</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $pageTitle }}</h1>
            <p class="mt-3 max-w-3xl text-slate-600">{{ $pageSubtitle }}</p>
        </div>

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-center text-sm font-semibold text-slate-700">
            Pembayaran manual • Verifikasi admin • Link download via email • Token aman
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Instruksi Pembayaran Manual</h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Metode Pembayaran</p>
                            <p class="mt-1 font-black text-slate-950">{{ $bankName }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Nominal Transfer</p>
                            <p class="mt-1 text-2xl font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Nomor Rekening / Tujuan</p>
                        <p class="mt-1 break-all text-xl font-black tracking-wide text-slate-950">{{ $bankAccountNumber }}</p>
                        <p class="mt-2 text-sm text-slate-600">a.n. {{ $bankAccountHolder }}</p>
                    </div>

                    @if (!empty($paymentNote))
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $paymentNote }}</p>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Upload Bukti Pembayaran</h2>

                    @if ($order->payment_proof_path)
                        <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-semibold text-blue-700">
                            Bukti pembayaran sudah diupload.
                            @if ($order->payment_uploaded_at)
                                <div class="mt-1 font-normal text-blue-600">Tanggal upload: {{ $order->payment_uploaded_at->format('d M Y H:i') }}</div>
                            @endif
                        </div>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                            Kirim Pemberitahuan ke Admin WhatsApp
                        </a>
                    @elseif ($order->status !== \App\Models\Order::STATUS_PAID)
                        <p class="mt-4 text-sm text-slate-600">
                            Upload bukti transfer dalam format JPG, PNG, atau PDF sesuai validasi yang berlaku.
                        </p>
                        <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">
                            Upload Bukti Pembayaran
                        </a>
                    @endif

                    @if ($order->status === \App\Models\Order::STATUS_REJECTED && !empty($order->rejection_reason))
                        <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                            Alasan penolakan: {{ $order->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ringkasan Invoice</p>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Invoice</span><span class="font-bold text-slate-950">{{ $order->invoice_number }}</span></div>
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
                            <img src="{{ asset($qrisImage) }}" alt="QRIS Ruang Cerdas" class="mx-auto w-full max-w-[220px] rounded-2xl border border-slate-200">
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-600">QRIS belum tersedia.</div>
                        @endif
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-black text-slate-950">Langkah Selanjutnya</h3>
                    @if ($order->status === \App\Models\Order::STATUS_PENDING)
                        <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm leading-6 text-slate-600">
                            <li>Transfer sesuai nominal.</li>
                            <li>Upload bukti pembayaran.</li>
                            <li>Tunggu verifikasi admin.</li>
                            <li>Link download dikirim ke email setelah paid.</li>
                        </ol>
                    @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                        <ul class="mt-4 space-y-2 text-sm leading-6 text-slate-600">
                            <li>Bukti sudah diterima.</li>
                            <li>Admin akan melakukan verifikasi.</li>
                            <li>Cek email setelah pembayaran disetujui.</li>
                        </ul>
                    @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                        <ul class="mt-4 space-y-2 text-sm leading-6 text-slate-600">
                            <li>Pembayaran sudah disetujui.</li>
                            <li>Link download sudah/sedang dikirim ke email.</li>
                            <li>Cek folder inbox/spam.</li>
                        </ul>
                    @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                        <ul class="mt-4 space-y-2 text-sm leading-6 text-slate-600">
                            <li>Pembayaran ditolak.</li>
                            <li>Periksa alasan penolakan.</li>
                            <li>Upload ulang bukti atau hubungi admin.</li>
                        </ul>
                    @else
                        <p class="mt-4 text-sm text-slate-600">Silakan cek status order secara berkala.</p>
                    @endif
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
                    Cek Email Download
                </span>
            @endif
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">
            Ruang Cerdas memproses pembayaran secara manual. Link download hanya dikirim setelah pembayaran disetujui admin.
        </p>
        <p class="mt-2 text-center text-sm text-slate-500">
            Jika bukti bayar sudah diupload, silakan tunggu verifikasi admin. Anda dapat cek status order kapan saja.
        </p>
    </div>
</section>
@endsection
