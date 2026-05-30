@extends('layouts.public')

@section('title', 'Upload Bukti Bayar - ' . $order->invoice_number)

@section('content')
@php
    $bankName = $paymentConfig['bank_name'] ?? 'Bank Mandiri';
    $bankAccountNumber = $paymentConfig['bank_account_number'] ?? '1234567890';
    $bankAccountHolder = $paymentConfig['bank_account_holder'] ?? 'Ruang Cerdas';
    $qrisImage = $paymentConfig['qris_image'] ?? null;
    $qrisExists = $qrisImage && file_exists(public_path($qrisImage));

    $statusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Menunggu Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi',
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
@endphp

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-6xl px-6">

        <div class="mb-8">
            <a href="{{ route('orders.thank-you', $order->invoice_number) }}"
               class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                ← Kembali ke invoice
            </a>

            <h1 class="mt-4 text-4xl font-black text-slate-950">
                Upload Bukti Bayar
            </h1>

            <p class="mt-3 text-slate-600">
                Upload bukti transfer atau QRIS untuk invoice {{ $order->invoice_number }}.
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">

            <div class="lg:col-span-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">

                    @if ($order->payment_proof_path)
                        <div class="mb-6 rounded-2xl bg-emerald-50 p-5 text-sm font-semibold text-emerald-700">
                            Bukti pembayaran sudah pernah diupload. Anda masih bisa upload ulang jika ingin mengganti bukti pembayaran.
                        </div>
                    @endif

                    <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-5">
                        <h2 class="text-lg font-black text-blue-950">
                            Pastikan pembayaran sudah dilakukan
                        </h2>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl bg-white p-4">
                                <p class="text-sm text-slate-500">Transfer Bank</p>
                                <p class="mt-1 font-black text-slate-950">{{ $bankName }}</p>
                                <p class="mt-1 text-lg font-black text-blue-600">{{ $bankAccountNumber }}</p>
                                <p class="mt-1 text-sm text-slate-600">a.n. {{ $bankAccountHolder }}</p>
                            </div>

                            <div class="rounded-2xl bg-white p-4">
                                <p class="text-sm text-slate-500">Total Bayar</p>
                                <p class="mt-1 text-2xl font-black text-blue-600">
                                    {{ \App\Support\Money::rupiah($order->price) }}
                                </p>
                                <p class="mt-1 text-sm text-slate-600">
                                    Transfer sesuai nominal invoice.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('orders.payment.upload', $order->invoice_number) }}"
                          enctype="multipart/form-data"
                          class="space-y-6">
                        @csrf

                        <div>
                            <label for="payment_proof" class="block text-sm font-bold text-slate-700">
                                File Bukti Pembayaran
                            </label>

                            <input
                                id="payment_proof"
                                name="payment_proof"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-blue-700"
                                required
                            >

                            <p class="mt-2 text-sm text-slate-500">
                                Format: JPG, JPEG, PNG, atau PDF. Maksimal 4 MB.
                            </p>

                            @error('payment_proof')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_note" class="block text-sm font-bold text-slate-700">
                                Catatan Pembayaran
                            </label>

                            <textarea
                                id="payment_note"
                                name="payment_note"
                                rows="4"
                                placeholder="Contoh: Transfer dari BCA atas nama Khairul Anwar"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:ring-blue-600"
                            >{{ old('payment_note', $order->payment_note) }}</textarea>

                            @error('payment_note')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-2xl bg-yellow-50 p-5 text-sm leading-6 text-yellow-800">
                            Pastikan nominal pembayaran sesuai invoice. Admin akan mengecek bukti bayar secara manual.
                        </div>

                        <button type="submit"
                                class="w-full rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                            Upload Bukti Bayar
                        </button>
                    </form>
                </div>
            </div>

            <aside>
                <div class="sticky top-24 space-y-6">

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                            Ringkasan Invoice
                        </p>

                        <h2 class="mt-4 text-2xl font-black text-slate-950">
                            {{ $order->invoice_number }}
                        </h2>

                        <div class="mt-6 space-y-4 text-sm">
                            <div class="flex justify-between gap-4">
                                <span class="text-slate-500">Produk</span>
                                <span class="text-right font-bold">{{ $order->product->name }}</span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-slate-500">Pembeli</span>
                                <span class="text-right font-bold">{{ $order->buyer_name }}</span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-slate-500">Total</span>
                                <span class="text-right text-xl font-black text-blue-600">
                                    {{ \App\Support\Money::rupiah($order->price) }}
                                </span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-slate-500">Status</span>
                                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm">
                        <h3 class="text-lg font-black text-emerald-950">
                            QRIS
                        </h3>

                        <div class="mt-4 rounded-2xl bg-white p-4 text-center">
                            @if ($qrisExists)
                                <img src="{{ asset($qrisImage) }}"
                                     alt="QRIS Ruang Cerdas"
                                     class="mx-auto w-full max-w-[200px] rounded-2xl border border-slate-200">
                            @else
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
                                    <p class="text-sm font-semibold text-slate-600">
                                        QRIS belum tersedia.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </aside>

        </div>
    </div>
</section>
@endsection