@extends('layouts.public')

@section('title', 'Hasil Tracking Order - Ruang Cerdas')
@section('meta_description', 'Status order pembelian produk digital Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@php
    $statusMessage = match($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Order dibuat, silakan lakukan pembayaran dan upload bukti bayar.',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti bayar sudah diterima, menunggu verifikasi admin.',
        \App\Models\Order::STATUS_PAID => 'Pembayaran disetujui. Cek email untuk link download.',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran ditolak. Silakan cek alasan penolakan atau hubungi admin.',
        default => 'Status order sedang diproses.',
    };
@endphp

@section('content')
<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-4xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Order Tracking</p>
                    <h1 class="mt-2 text-2xl font-black text-slate-950">Invoice {{ $order->invoice_number }}</h1>
                </div>
                <a href="{{ route('public.order-tracking.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                    Cek Invoice Lain
                </a>
            </div>

            <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                {{ $statusMessage }}
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Nama Pembeli</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $order->buyer_name }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Status Order</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Produk</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $order->product?->name ?? '-' }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Tanggal Order</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Upload Bukti Bayar</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $order->payment_uploaded_at?->format('d M Y H:i') ?? '-' }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Tanggal Paid</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $order->paid_at?->format('d M Y H:i') ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                <div class="text-xs text-slate-500">Rincian Pembayaran</div>
                @if ((float) ($order->discount_amount ?? 0) > 0)
                    <div class="mt-2 text-sm text-slate-700">Harga Asli: {{ \App\Support\Money::format((float) ($order->original_price ?? 0)) }}</div>
                    <div class="mt-1 text-sm text-slate-700">Kupon: {{ $order->coupon_code ?? '-' }}</div>
                    <div class="mt-1 text-sm text-slate-700">Diskon: {{ \App\Support\Money::format((float) $order->discount_amount) }}</div>
                @endif
                <div class="mt-2 font-semibold text-slate-900">Total Bayar: {{ \App\Support\Money::format($order->price ?? 0) }}</div>
            </div>

            @if ($order->status === \App\Models\Order::STATUS_PAID)
                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    Pembayaran disetujui. Link download dikirim ke email pembeli.
                </div>
            @endif

            @if ($order->status === \App\Models\Order::STATUS_REJECTED && filled($order->rejection_reason))
                <div class="mt-6 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                    Alasan penolakan: {{ $order->rejection_reason }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
