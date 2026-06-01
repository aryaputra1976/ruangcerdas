@extends('layouts.public')

@section('title', 'Upload Bukti Bayar - ' . $order->invoice_number)
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
    $qrisExists = $qrisImage && file_exists(public_path($qrisImage));
    $hasBankInstruction = $bankAccounts->isNotEmpty();
    $hasPaymentInstruction = $hasBankInstruction || $qrisExists;

    $statusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Menunggu Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi',
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
@endphp

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8">
            <a href="{{ route('orders.thank-you', $order->invoice_number) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                Kembali ke invoice
            </a>
            <p class="mt-5 inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">Pembayaran</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Upload Bukti Pembayaran</h1>
            <p class="mt-3 text-slate-600">Upload bukti transfer untuk invoice {{ $order->invoice_number }} agar admin bisa memverifikasi pembayaran Anda.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                    @if ($order->payment_proof_path)
                        <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-semibold text-blue-700">
                            Bukti pembayaran sudah diupload.
                            @if ($order->payment_uploaded_at)
                                <div class="mt-1 font-normal text-blue-600">Tanggal upload: {{ $order->payment_uploaded_at->format('d M Y H:i') }}</div>
                            @endif
                        </div>
                    @endif

                    <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-5">
                        <h2 class="text-lg font-black text-blue-950">Pastikan pembayaran sudah dilakukan</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl bg-white p-4">
                                @if ($hasBankInstruction)
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm text-slate-500">Rekening Tujuan</p>
                                        @if ($bankAccounts->count() > 1)
                                            <p class="text-xs font-semibold text-slate-500">Pilih salah satu rekening di bawah untuk transfer manual.</p>
                                        @endif
                                    </div>
                                    <div class="mt-2 space-y-2">
                                        @foreach ($bankAccounts as $account)
                                            <div class="rounded-xl border {{ $account['is_primary'] ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-slate-50' }} p-3">
                                                <div class="flex items-center justify-between gap-2">
                                                    <p class="font-black text-slate-950">{{ $account['bank_name'] }}</p>
                                                    @if ($account['is_primary'])
                                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-blue-700">Utama</span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-lg font-black text-blue-600 break-all">{{ $account['account_number'] }}</p>
                                                <p class="mt-1 text-sm text-slate-600">a.n. {{ $account['account_holder'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Instruksi Pembayaran</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-700">Instruksi pembayaran belum tersedia. Silakan hubungi admin Ruang Cerdas.</p>
                                @endif
                            </div>
                            <div class="rounded-2xl bg-white p-4">
                                <p class="text-sm text-slate-500">Total Bayar</p>
                                <p class="mt-1 text-2xl font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</p>
                                <p class="mt-1 text-sm text-slate-600">Transfer sesuai nominal invoice.</p>
                            </div>
                        </div>
                    </div>

                    @if (! $hasPaymentInstruction)
                        <div class="mb-6 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800">
                            Instruksi pembayaran belum tersedia. Silakan hubungi admin Ruang Cerdas.
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            Upload gagal. Silakan periksa file dan coba lagi.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('orders.payment.upload', $order->invoice_number) }}" enctype="multipart/form-data" class="space-y-6" onsubmit="window.rcTrack && window.rcTrack('PurchasePending', {source: 'upload_payment'});">
                        @csrf
                        <div>
                            <label for="payment_proof" class="block text-sm font-bold text-slate-700">File Bukti Pembayaran</label>
                            <input id="payment_proof" name="payment_proof" type="file" accept=".jpg,.jpeg,.png,.pdf" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-blue-700" required>
                            <p class="mt-2 text-sm text-slate-500">Format yang diterima: JPG, JPEG, PNG, atau PDF. Gunakan bukti yang jelas agar verifikasi lebih mudah.</p>
                            @error('payment_proof')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800">
                            Upload bukti pembayaran yang jelas, lengkap, dan sesuai nominal agar proses verifikasi berjalan lebih cepat.
                        </div>

                        <div>
                            <label for="payment_note" class="block text-sm font-bold text-slate-700">Catatan Pembayaran</label>
                            <textarea id="payment_note" name="payment_note" rows="4" placeholder="Contoh: Transfer dari BCA atas nama Khairul Anwar" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:ring-blue-600">{{ old('payment_note', $order->payment_note) }}</textarea>
                            <p class="mt-2 text-sm text-slate-500">Opsional. Bisa diisi jika ada informasi tambahan yang membantu admin memverifikasi pembayaran.</p>
                            @error('payment_note')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                            Upload Bukti Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ringkasan Invoice</p>
                    <h2 class="mt-4 break-all text-2xl font-black text-slate-950">{{ $order->invoice_number }}</h2>
                    <div class="mt-6 space-y-3 text-sm">
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Produk</span><span class="font-bold text-slate-950 text-right">{{ $order->product->name }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Pembeli</span><span class="font-bold text-slate-950">{{ $order->buyer_name }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Total</span><span class="text-xl font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Status</span><span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">{{ $statusLabel }}</span></div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-black text-slate-950">Status Saat Ini</h3>
                    @if ($order->status === \App\Models\Order::STATUS_PENDING)
                        <p class="mt-4 text-sm leading-6 text-slate-600">Order masih menunggu pembayaran. Silakan transfer lalu upload bukti pembayaran.</p>
                    @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                        <p class="mt-4 text-sm leading-6 text-slate-600">Bukti pembayaran sudah diterima dan saat ini menunggu verifikasi admin.</p>
                    @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                        <p class="mt-4 text-sm leading-6 text-slate-600">Pembayaran sudah disetujui. Silakan cek email untuk link download produk Anda.</p>
                    @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                        <p class="mt-4 text-sm leading-6 text-slate-600">Pembayaran ditolak. Silakan cek alasan penolakan di halaman invoice lalu upload ulang bukti jika diperlukan.</p>
                    @else
                        <p class="mt-4 text-sm leading-6 text-slate-600">Silakan cek status order Anda secara berkala.</p>
                    @endif
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
    </div>
</section>
@endsection
