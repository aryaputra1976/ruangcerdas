@extends('layouts.public')

@section('title', 'Hasil Tracking Order - Ruang Cerdas')
@section('meta_description', 'Status order pembelian produk digital Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@php
    $statusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Menunggu Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi Admin',
        \App\Models\Order::STATUS_PAID => 'Pembayaran Disetujui',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran Ditolak',
        default => ucfirst(str_replace('_', ' ', (string) $order->status)),
    };

    $statusClass = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'bg-amber-100 text-amber-700',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'bg-blue-100 text-blue-700',
        \App\Models\Order::STATUS_PAID => 'bg-emerald-100 text-emerald-700',
        \App\Models\Order::STATUS_REJECTED => 'bg-red-100 text-red-700',
        default => 'bg-slate-100 text-slate-700',
    };

    $statusMessage = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Order dibuat, silakan lakukan pembayaran dan upload bukti bayar.',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti bayar sudah diterima, menunggu verifikasi admin.',
        \App\Models\Order::STATUS_PAID => 'Pembayaran sudah disetujui. Link download akan tersedia atau sudah dikirim ke email pembeli.',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran ditolak. Silakan cek alasan penolakan dan upload ulang bukti jika diperlukan.',
        default => 'Status order sedang diproses. Silakan cek kembali secara berkala.',
    };
@endphp

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Order Tracking</p>
                    <h1 class="mt-2 break-all text-2xl font-black text-slate-950 md:text-3xl">{{ $order->invoice_number }}</h1>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-bold uppercase {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                        Cek Invoice Lain
                    </a>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-800">
                {{ $statusMessage }}
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(280px,360px)]">
                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-lg font-black text-slate-950">Ringkasan Pembeli dan Produk</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Nama Pembeli</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->buyer_name }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Tanggal Order</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 md:col-span-2">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Produk</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->product?->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-lg font-black text-slate-950">Status Pembayaran</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Status Order</div>
                                <div class="mt-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Total Bayar</div>
                                <div class="mt-2 text-xl font-black text-blue-600">{{ \App\Support\Money::format($order->price ?? 0) }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Bukti Bayar Diupload</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->payment_uploaded_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Pembayaran Disetujui</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->paid_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                        </div>

                        @if ((float) ($order->discount_amount ?? 0) > 0)
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
                                <div>Harga Asli: {{ \App\Support\Money::format((float) ($order->original_price ?? 0)) }}</div>
                                <div class="mt-1">Kupon: {{ $order->coupon_code ?? '-' }}</div>
                                <div class="mt-1">Diskon: {{ \App\Support\Money::format((float) $order->discount_amount) }}</div>
                            </div>
                        @endif
                    </div>

                    @if ($order->status === \App\Models\Order::STATUS_REJECTED && filled($order->rejection_reason))
                        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-4 text-sm leading-6 text-red-700">
                            Alasan penolakan: {{ $order->rejection_reason }}
                        </div>
                    @endif
                </div>

                <aside class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black text-slate-950">Tindakan Berikutnya</h2>

                        @if ($order->status === \App\Models\Order::STATUS_PENDING)
                            <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Lakukan pembayaran sesuai instruksi invoice Anda.</div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Setelah transfer, upload bukti pembayaran agar admin bisa memverifikasi.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                            <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Bukti pembayaran sudah diterima.</div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Silakan tunggu proses verifikasi admin. Tidak perlu upload ulang kecuali diminta.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                            <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Pembayaran sudah disetujui.</div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Cek email inbox dan folder spam untuk link download produk.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                            <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Periksa alasan penolakan pembayaran Anda.</div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Siapkan bukti pembayaran yang lebih jelas lalu upload ulang jika diperlukan.</div>
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                Silakan cek status order Anda kembali beberapa saat lagi.
                            </div>
                        @endif

                        <div class="mt-5 flex flex-col gap-3">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                                Kembali ke Produk
                            </a>
                            <a href="{{ route('public.order-tracking.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                                Kembali ke Form Tracking
                            </a>
                            @if ($order->status !== \App\Models\Order::STATUS_PAID)
                                <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                                    Upload Bukti Pembayaran
                                </a>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
