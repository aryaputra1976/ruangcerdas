@extends('layouts.public')

@section('title', 'Hasil Status Order - Ruang Cerdas')
@section('meta_description', 'Status order pembelian produk digital Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@php
    $tryoutPackage = $order->product?->slug
        ? \App\Models\TryoutPackage::query()->where('slug', $order->product->slug)->first()
        : null;
    $isTryoutOrder = (bool) $tryoutPackage;

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
        \App\Models\Order::STATUS_PENDING => 'Order dibuat, silakan lakukan pembayaran dan upload bukti pembayaran.',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti bayar sudah diterima, menunggu verifikasi admin.',
        \App\Models\Order::STATUS_PAID => $isTryoutOrder
            ? 'Pembayaran sudah disetujui. Akses tryout aktif dan bisa dibuka dari halaman tryout dengan email pembelian yang sama.'
            : 'Pembayaran sudah disetujui. Produk digital dapat dibuka melalui Ruang Akses menggunakan email pembeli dan invoice.',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran ditolak. Silakan cek alasan penolakan dan upload ulang bukti jika diperlukan.',
        default => 'Status order sedang diproses. Silakan cek kembali secara berkala.',
    };
@endphp

@section('content')
<section class="bg-slate-50 pt-3 pb-6 md:pt-4 md:pb-8">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Status Order</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">
                            {{ $isTryoutOrder ? 'Akses Tryout' : 'Ruang Akses' }}
                        </span>
                        <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-700">
                            {{ $isTryoutOrder ? 'Produk Tryout' : 'Produk Digital' }}
                        </span>
                    </div>
                    <h1 class="mt-2 break-all text-2xl font-black text-slate-950 md:text-3xl">{{ $order->invoice_number }}</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Lihat status terbaru dan lanjutkan hanya ke aksi yang masih dibutuhkan.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-bold uppercase {{ $statusClass }} md:self-start">
                        {{ $statusLabel }}
                    </span>
                    <a href="{{ route('public.order-tracking.index') }}" class="rc-btn-neutral px-4 py-2 text-sm font-semibold">
                        Cek Status Lain
                    </a>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-800">
                {{ $statusMessage }}
            </div>

            <div class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-5">
                    <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-5">
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Produk</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->product?->name ?? '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Pembeli</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->buyer_name }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Tanggal Order</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Total Bayar</div>
                                <div class="mt-2 text-xl font-black text-blue-600">{{ \App\Support\Money::format($order->price ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-lg font-black text-slate-950">Detail Singkat</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Bukti Bayar Diupload</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->payment_uploaded_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Pembayaran Disetujui</div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $order->paid_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 md:col-span-2">
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Email Akses</div>
                                <div class="mt-2 break-all font-semibold text-slate-900">{{ $order->buyer_email }}</div>
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
                        <h2 class="text-lg font-black text-slate-950">Aksi Sekarang</h2>

                        @if ($order->status === \App\Models\Order::STATUS_PENDING)
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                Lakukan pembayaran sesuai invoice, lalu upload bukti pembayaran agar admin bisa memverifikasi.
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                Bukti pembayaran sudah diterima. Tunggu verifikasi admin, tidak perlu upload ulang kecuali diminta.
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                @if ($isTryoutOrder)
                                    Pembayaran sudah disetujui. Lanjutkan ke halaman tryout dan mulai memakai email pembelian yang sama.
                                @else
                                    Pembayaran sudah disetujui. Lanjutkan ke Ruang Akses menggunakan email pembeli dan invoice yang sama.
                                @endif
                            </div>
                        @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                Periksa alasan penolakan, lalu upload ulang bukti pembayaran yang lebih jelas jika masih ingin melanjutkan order ini.
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                                Silakan cek status order Anda kembali beberapa saat lagi.
                            </div>
                        @endif

                        <div class="mt-5 flex flex-col gap-3">
                            @if ($order->status === \App\Models\Order::STATUS_PAID)
                                @if ($isTryoutOrder)
                                    <a href="{{ route($tryoutPackage->listingRouteName()) }}" class="rc-btn-success px-5 py-3 text-sm">
                                        Mulai Tryout Sekarang
                                    </a>
                                    <a href="{{ route('public.tryouts.packages.start', ['tryoutType' => $tryoutPackage->routeSegment(), 'tryoutPackage' => $tryoutPackage]) }}" class="rc-btn-neutral px-5 py-3 text-sm">
                                        Lihat Halaman Paket
                                    </a>
                                @else
                                    <a href="{{ route('public.download-room.index') }}" class="rc-btn-success px-5 py-3 text-sm">
                                        Buka Ruang Akses
                                    </a>
                                @endif
                            @endif
                            @if ($order->status !== \App\Models\Order::STATUS_PAID)
                                <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="rc-btn-primary px-5 py-3 text-sm">
                                    Upload Bukti Pembayaran
                                </a>
                            @endif
                            <a href="{{ route('products.index') }}" class="rc-btn-neutral px-5 py-3 text-sm">
                                Kembali ke Produk
                            </a>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black text-slate-950">Panduan Cepat</h2>
                        <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Invoice: <span class="font-semibold text-slate-900">{{ $order->invoice_number }}</span></div>
                            @if ($order->status === \App\Models\Order::STATUS_PAID)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    @if ($isTryoutOrder)
                                        Gunakan email pembelian yang sama saat mulai tryout.
                                    @else
                                        Gunakan email dan invoice yang sama saat masuk ke Ruang Akses.
                                    @endif
                                </div>
                            @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Admin sedang memeriksa bukti pembayaran Anda.</div>
                            @elseif ($order->status === \App\Models\Order::STATUS_PENDING || $order->status === \App\Models\Order::STATUS_REJECTED)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">Setelah pembayaran disetujui, akses produk akan dibuka dari halaman berikutnya.</div>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
