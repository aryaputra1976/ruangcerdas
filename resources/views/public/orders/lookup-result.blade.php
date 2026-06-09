@extends('layouts.public')

@section('title', 'Hasil Cek Order - Ruang Cerdas')
@section('meta_description', 'Hasil pencarian order pembelian produk digital Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@php
    $statusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Pending',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi',
        \App\Models\Order::STATUS_PAID => 'Paid',
        \App\Models\Order::STATUS_REJECTED => 'Rejected',
        \App\Models\Order::STATUS_EXPIRED => 'Expired',
        default => ucfirst(str_replace('_', ' ', (string) $order->status)),
    };

    $statusClass = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'bg-amber-100 text-amber-700',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'bg-blue-100 text-blue-700',
        \App\Models\Order::STATUS_PAID => 'bg-emerald-100 text-emerald-700',
        \App\Models\Order::STATUS_REJECTED => 'bg-red-100 text-red-700',
        \App\Models\Order::STATUS_EXPIRED => 'bg-slate-200 text-slate-700',
        default => 'bg-slate-100 text-slate-700',
    };

    $hasValidDownload = $order->status === \App\Models\Order::STATUS_PAID
        && filled($order->download_token)
        && filled($order->download_expires_at)
        && $order->download_expires_at->isFuture();
@endphp

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Cek Order</p>
                    <h1 class="mt-2 break-all text-2xl font-black text-slate-950 md:text-3xl">{{ $order->invoice_number }}</h1>
                </div>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-bold uppercase {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <a href="{{ route('public.orders.lookup') }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                        Cek Order Lain
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(280px,360px)]">
                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-lg font-black text-slate-950">Detail Order</h2>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Nomor Invoice</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->invoice_number }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Tanggal Order</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Nama Pembeli</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->buyer_name }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Email Pembeli</div>
                                <div class="mt-2 font-semibold text-slate-900 break-all">{{ $order->buyer_email }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 md:col-span-2">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Nama Produk</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->product?->name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Status Order</div>
                                <div class="mt-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Harga</div>
                                <div class="mt-2 text-xl font-black text-blue-600">{{ \App\Support\Money::format($order->price ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    @if ($order->status === \App\Models\Order::STATUS_REJECTED && filled($order->rejection_reason))
                        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-4 text-sm leading-6 text-red-700">
                            Alasan penolakan: {{ $order->rejection_reason }}
                        </div>
                    @endif
                </div>

                <aside class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black text-slate-950">Instruksi Selanjutnya</h2>

                        @if ($order->status === \App\Models\Order::STATUS_PENDING)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Order Anda masih pending. Silakan lakukan pembayaran dan upload bukti pembayaran.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Bukti pembayaran sudah diterima dan saat ini menunggu verifikasi admin.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Pembayaran sudah disetujui. Anda bisa melanjutkan download produk jika link masih aktif.</div>
                                @unless ($hasValidDownload)
                                    <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-amber-800">
                                        Link download saat ini tidak aktif atau sudah kedaluwarsa. Silakan hubungi admin bila Anda masih membutuhkan akses.
                                    </div>
                                @endunless
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Pembayaran ditolak. Periksa alasan penolakan lalu upload ulang bukti pembayaran bila diperlukan.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_EXPIRED)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Order ini sudah kedaluwarsa. Silakan checkout ulang dari halaman produk untuk membuat order baru.</div>
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                Silakan cek order Anda kembali beberapa saat lagi.
                            </div>
                        @endif

                        <div class="mt-5 flex flex-col gap-3">
                            @if ($order->status === \App\Models\Order::STATUS_PENDING)
                                <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                                    Upload Bukti Pembayaran
                                </a>
                            @endif

                            @if ($order->status === \App\Models\Order::STATUS_REJECTED)
                                <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                                    Upload Ulang Bukti Pembayaran
                                </a>
                            @endif

                            @if ($hasValidDownload)
                                <a href="{{ route('orders.download', ['invoice' => $order->invoice_number, 'token' => $order->download_token]) }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-700">
                                    Download Produk
                                </a>
                            @endif

                            @if ($order->status === \App\Models\Order::STATUS_EXPIRED)
                                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-slate-700">
                                    Checkout Ulang
                                </a>
                            @endif

                            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:border-blue-600 hover:text-blue-600">
                                Kembali ke Produk
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
