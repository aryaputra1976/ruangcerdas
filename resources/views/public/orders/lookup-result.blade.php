@extends('layouts.public')

@section('title', 'Hasil Status Order - Ruang Cerdas')
@section('meta_description', 'Hasil pencarian order pembelian produk digital Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@php
    $statusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Menunggu Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi Admin',
        \App\Models\Order::STATUS_PAID => 'Pembayaran Disetujui',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran Ditolak',
        \App\Models\Order::STATUS_EXPIRED => 'Order Kedaluwarsa',
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

    $statusMessage = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Order masih menunggu pembayaran. Silakan selesaikan pembayaran lalu upload bukti pembayaran.',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti pembayaran sudah diterima dan saat ini menunggu verifikasi admin.',
        \App\Models\Order::STATUS_PAID => 'Pembayaran sudah disetujui. Akses produk dilanjutkan dari Ruang Akses.',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran ditolak. Periksa alasan penolakan dan upload ulang bukti bila diperlukan.',
        \App\Models\Order::STATUS_EXPIRED => 'Order ini sudah kedaluwarsa dan tidak dapat diproses lebih lanjut.',
        default => 'Status order sedang diproses.',
    };

    $primaryActionLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Upload Bukti Pembayaran',
        \App\Models\Order::STATUS_REJECTED => 'Upload Ulang Bukti Pembayaran',
        \App\Models\Order::STATUS_PAID => 'Buka Ruang Akses',
        \App\Models\Order::STATUS_EXPIRED => 'Checkout Ulang',
        default => null,
    };

    $primaryActionUrl = match ($order->status) {
        \App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_REJECTED => route('orders.payment.form', $order->invoice_number),
        \App\Models\Order::STATUS_PAID => route('public.download-room.index'),
        \App\Models\Order::STATUS_EXPIRED => route('products.index'),
        default => null,
    };

    $primaryActionClass = match ($order->status) {
        \App\Models\Order::STATUS_PAID => 'bg-emerald-600 text-white hover:bg-emerald-700',
        \App\Models\Order::STATUS_EXPIRED => 'bg-slate-900 text-white hover:bg-slate-700',
        \App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_REJECTED => 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-300/40 hover:bg-amber-300',
        default => 'bg-blue-600 text-white hover:bg-blue-700',
    };
@endphp

@section('content')
<section class="bg-slate-50 pt-4 pb-8 md:pt-5 md:pb-8">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Status Order</p>
                    <h1 class="mt-2 break-all text-2xl font-black text-slate-950 md:text-3xl">{{ $order->invoice_number }}</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Ringkasan cepat order dan langkah berikutnya berdasarkan status pembayaran terbaru.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-bold uppercase {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <a href="{{ route('public.orders.lookup') }}" class="rc-btn-neutral px-4 py-2 text-sm font-semibold">
                        Cek Status Lain
                    </a>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-800">
                {{ $statusMessage }}
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
                        <h2 class="text-lg font-black text-slate-950">Aksi Sekarang</h2>

                        @if ($order->status === \App\Models\Order::STATUS_PENDING)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Selesaikan pembayaran order ini, lalu upload bukti pembayaran agar admin bisa memverifikasi.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Bukti pembayaran sudah diterima. Tidak perlu upload ulang kecuali diminta admin.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Pembayaran sudah disetujui. Lanjutkan ke Ruang Akses untuk membuka produk digital Anda.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Periksa alasan penolakan lalu upload ulang bukti pembayaran bila masih ingin melanjutkan order ini.</div>
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_EXPIRED)
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Order ini sudah kedaluwarsa. Silakan checkout ulang dari halaman produk untuk membuat order baru.</div>
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                Silakan cek status order Anda kembali beberapa saat lagi.
                            </div>
                        @endif

                        <div class="mt-5 flex flex-col gap-3">
                            @if ($primaryActionLabel && $primaryActionUrl)
                                <a href="{{ $primaryActionUrl }}" class="inline-flex items-center justify-center px-5 py-3 text-sm {{ $primaryActionClass }}">
                                    {{ $primaryActionLabel }}
                                </a>
                            @endif

                            <a href="{{ route('products.index') }}" class="rc-btn-neutral px-5 py-3 text-sm">
                                Kembali ke Produk
                            </a>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black text-slate-950">Panduan cepat</h2>
                        <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Invoice: <span class="font-bold">{{ $order->invoice_number }}</span></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Email akses: <span class="font-bold break-all">{{ $order->buyer_email }}</span></div>
                            @if ($order->status === \App\Models\Order::STATUS_PAID)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Gunakan data yang sama saat masuk ke Ruang Akses.</div>
                            @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Admin akan memeriksa bukti pembayaran sebelum akses produk dibuka.</div>
                            @elseif ($order->status === \App\Models\Order::STATUS_PENDING || $order->status === \App\Models\Order::STATUS_REJECTED)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Setelah pembayaran disetujui, produk dibuka dari Ruang Akses.</div>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
