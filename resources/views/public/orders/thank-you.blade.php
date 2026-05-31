@extends('layouts.public')

@section('title', 'Instruksi Pembayaran - ' . $order->invoice_number)

@section('content')
@php
    $bankName = $paymentConfig['bank_name'] ?? 'Bank Mandiri';
    $bankAccountNumber = $paymentConfig['bank_account_number'] ?? '1234567890';
    $bankAccountHolder = $paymentConfig['bank_account_holder'] ?? 'Ruang Cerdas';
    $qrisImage = $paymentConfig['qris_image'] ?? null;
    $paymentNote = $paymentConfig['payment_note'] ?? 'Transfer sesuai nominal invoice agar verifikasi lebih cepat.';

    $qrisExists = $qrisImage && file_exists(public_path($qrisImage));

    $statusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Menunggu Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi Admin',
        \App\Models\Order::STATUS_PAID => 'Pembayaran Disetujui',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran Ditolak',
        default => str_replace('_', ' ', $order->status),
    };

    $statusClass = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'bg-yellow-100 text-yellow-700',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'bg-blue-100 text-blue-700',
        \App\Models\Order::STATUS_PAID => 'bg-emerald-100 text-emerald-700',
        \App\Models\Order::STATUS_REJECTED => 'bg-red-100 text-red-700',
        default => 'bg-slate-100 text-slate-700',
    };

    $downloadUrl = ($order->status === \App\Models\Order::STATUS_PAID && $order->download_token)
        ? route('orders.download', [$order->invoice_number, $order->download_token])
        : null;
@endphp

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-6xl px-6">

        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 p-5 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($order->status === \App\Models\Order::STATUS_PAID)
            <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-5 text-sm font-semibold text-emerald-700">
                Pembayaran sudah disetujui. Silakan download produk Anda melalui tombol download di bawah.
            </div>
        @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
            <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm font-semibold text-blue-700">
                Bukti pembayaran sudah diterima. Admin akan melakukan verifikasi secepatnya.
            </div>
        @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-5 text-sm font-semibold text-red-700">
                Pembayaran ditolak. Silakan upload ulang bukti bayar yang benar atau hubungi admin.
            </div>
        @endif

        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm md:p-10">

            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-start">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                        Invoice Berhasil Dibuat
                    </p>

                    <h1 class="mt-4 text-3xl font-black text-slate-950 md:text-5xl">
                        Terima kasih, {{ $order->buyer_name }}.
                    </h1>

                    <p class="mt-4 max-w-2xl text-slate-600">
                        Silakan lakukan pembayaran sesuai total invoice. Setelah pembayaran selesai,
                        upload bukti bayar agar admin dapat melakukan verifikasi.
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-100 px-5 py-4 text-right">
                    <p class="text-sm text-slate-500">Invoice</p>
                    <p class="mt-1 text-xl font-black text-slate-950">
                        {{ $order->invoice_number }}
                    </p>

                    <div class="mt-3">
                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">

                <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-xl font-black text-slate-950">
                        Detail Order
                    </h2>

                    <div class="mt-5 space-y-4 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Produk</span>
                            <span class="text-right font-bold text-slate-950">
                                {{ $order->product->name }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Email</span>
                            <span class="break-all text-right font-bold text-slate-950">
                                {{ $order->buyer_email }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">WhatsApp</span>
                            <span class="text-right font-bold text-slate-950">
                                {{ $order->buyer_whatsapp }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Tanggal</span>
                            <span class="text-right font-bold text-slate-950">
                                {{ $order->created_at?->format('d M Y H:i') }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 border-t border-slate-200 pt-4">
                            <span class="text-slate-500">Total Bayar</span>
                            <span class="text-right text-2xl font-black text-blue-600">
                                {{ \App\Support\Money::rupiah($order->price) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-blue-100 bg-blue-50 p-6">
                    <h2 class="text-xl font-black text-blue-950">
                        Transfer Bank
                    </h2>

                    <div class="mt-5 rounded-2xl bg-white p-5 text-sm">
                        <p class="text-slate-500">Bank</p>
                        <p class="mt-1 text-lg font-black text-slate-950">
                            {{ $bankName }}
                        </p>

                        <p class="mt-4 text-slate-500">Nomor Rekening</p>
                        <div class="mt-1 rounded-xl bg-slate-50 px-4 py-3">
                            <p class="break-all text-xl font-black tracking-wide text-slate-950">
                                {{ $bankAccountNumber }}
                            </p>
                        </div>

                        <p class="mt-4 text-slate-500">Atas Nama</p>
                        <p class="mt-1 font-bold text-slate-950">
                            {{ $bankAccountHolder }}
                        </p>

                        <p class="mt-4 text-slate-500">Nominal Transfer</p>
                        <p class="mt-1 text-2xl font-black text-blue-600">
                            {{ \App\Support\Money::rupiah($order->price) }}
                        </p>
                    </div>

                    <p class="mt-4 text-sm font-semibold leading-6 text-blue-900">
                        {{ $paymentNote }}
                    </p>
                </div>

                <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50 p-6">
                    <h2 class="text-xl font-black text-emerald-950">
                        QRIS
                    </h2>

                    <div class="mt-5 rounded-2xl bg-white p-5 text-center">
                        @if ($qrisExists)
                            <img src="{{ asset($qrisImage) }}"
                                 alt="QRIS Ruang Cerdas"
                                 class="mx-auto w-full max-w-[240px] rounded-2xl border border-slate-200">
                        @else
                            <div class="flex min-h-[220px] items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6">
                                <div>
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-2xl font-black text-emerald-700">
                                        QR
                                    </div>

                                    <p class="mt-4 text-sm font-semibold text-slate-600">
                                        Gambar QRIS belum tersedia.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Simpan QRIS di public/images/payment/qris-ruangcerdas.png
                                    </p>
                                </div>
                            </div>
                        @endif

                        <p class="mt-4 text-sm text-slate-600">
                            Scan QRIS, lalu bayar sesuai nominal invoice:
                        </p>

                        <p class="mt-1 text-xl font-black text-emerald-700">
                            {{ \App\Support\Money::rupiah($order->price) }}
                        </p>
                    </div>
                </div>

            </div>

            <div class="mt-8 rounded-[2rem] border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-xl font-black text-slate-950">
                    Langkah Berikutnya
                </h2>

                <div class="mt-5 grid gap-4 md:grid-cols-4">
                    <div class="rounded-2xl bg-white p-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-sm font-black text-white">
                            1
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-950">Bayar Invoice</p>
                    </div>

                    <div class="rounded-2xl bg-white p-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-sm font-black text-white">
                            2
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-950">Upload Bukti</p>
                    </div>

                    <div class="rounded-2xl bg-white p-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-sm font-black text-white">
                            3
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-950">Admin Verifikasi</p>
                    </div>

                    <div class="rounded-2xl bg-white p-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-sm font-black text-white">
                            4
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-950">Download Aktif</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                @if ($downloadUrl)
                    <a href="{{ $downloadUrl }}"
                       class="flex-1 rounded-2xl bg-emerald-600 px-6 py-4 text-center font-bold text-white hover:bg-emerald-700">
                        Download Produk
                    </a>
                @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                    <a href="{{ route('orders.payment.form', $order->invoice_number) }}"
                       class="flex-1 rounded-2xl bg-blue-600 px-6 py-4 text-center font-bold text-white hover:bg-blue-700">
                        Ganti / Upload Ulang Bukti Bayar
                    </a>
                @else
                    <a href="{{ route('orders.payment.form', $order->invoice_number) }}"
                       class="flex-1 rounded-2xl bg-blue-600 px-6 py-4 text-center font-bold text-white hover:bg-blue-700">
                        Upload Bukti Bayar
                    </a>
                @endif

                <a href="{{ route('products.index') }}"
                   class="flex-1 rounded-2xl border border-slate-300 bg-white px-6 py-4 text-center font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                    Lihat Produk Lain
                </a>
            </div>

        </div>
    </div>
</section>
@endsection