@extends('layouts.public')

@section('title', 'Upload Bukti Pembayaran - ' . $order->invoice_number)
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
    $qrisStorageExists = filled($qrisImage) && \Illuminate\Support\Facades\Storage::disk('public')->exists($qrisImage);
    $qrisPublicExists = filled($qrisImage) && file_exists(public_path($qrisImage));
    $qrisExists = $qrisStorageExists || $qrisPublicExists;
    $qrisUrl = $qrisStorageExists
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($qrisImage)
        : ($qrisPublicExists ? asset($qrisImage) : null);
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

    $waSupportUrl = \App\Support\WhatsApp::waMeUrl(
        $supportWhatsapp ?? null,
        'Halo Admin Ruang Cerdas, saya butuh bantuan untuk invoice ' . $order->invoice_number . '.'
    );
    $isTryoutOrder = $order->product?->product_type === 'tryout';
@endphp

<section class="bg-slate-50 pt-3 pb-6 md:pt-4 md:pb-8">
    <div class="mx-auto max-w-5xl px-6">
        <div class="mb-4">
            <a href="{{ route('orders.thank-you', $order->invoice_number) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                Kembali ke Invoice
            </a>
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">
                    {{ $isTryoutOrder ? 'Akses Tryout' : 'Ruang Akses' }}
                </span>
                <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-700">
                    {{ $isTryoutOrder ? 'Produk Tryout' : 'Produk Digital' }}
                </span>
            </div>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Upload Bukti Pembayaran</h1>
            <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600 md:text-base">
                Upload bukti transfer untuk invoice {{ $order->invoice_number }} agar admin bisa memverifikasi pembayaran Anda.
            </p>
        </div>

        <div class="space-y-5">
            @if (! $hasPaymentInstruction)
                <div class="rounded-[2rem] border border-amber-100 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800">
                    Instruksi pembayaran belum tersedia. Silakan hubungi admin Ruang Cerdas.
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-[2rem] border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    Upload gagal. Silakan periksa file dan coba lagi.
                </div>
            @endif

            <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-5">
                    <div id="payment_proof" class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm scroll-mt-24">
                        @if ($order->payment_proof_path)
                            <div class="mb-5 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-semibold text-blue-700">
                                Bukti pembayaran sudah diupload.
                                @if ($order->payment_uploaded_at)
                                    <div class="mt-1 font-normal text-blue-600">Tanggal upload: {{ $order->payment_uploaded_at->format('d M Y H:i') }}</div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <span><span class="font-semibold text-slate-900">Invoice:</span> {{ $order->invoice_number }}</span>
                            <span><span class="font-semibold text-slate-900">Produk:</span> {{ $order->product->name }}</span>
                            <span><span class="font-semibold text-slate-900">Total:</span> <span class="font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</span></span>
                        </div>

                        @if ($hasBankInstruction)
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <div class="flex flex-col gap-1 md:flex-row md:items-end md:justify-between">
                                    <div>
                                        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Rekening Tujuan</p>
                                        <p class="mt-1 text-sm text-slate-600">Transfer sesuai nominal ke salah satu rekening berikut.</p>
                                    </div>
                                    @if ($bankAccounts->count() > 1)
                                        <p class="text-xs font-semibold text-slate-500">Pilih salah satu rekening.</p>
                                    @endif
                                </div>
                                <div class="mt-3 space-y-2 text-sm leading-7 text-slate-700">
                                    @foreach ($bankAccounts as $account)
                                        <p>
                                            <span class="font-semibold text-slate-950">{{ $account['bank_name'] }}</span>
                                            <span class="mx-1 text-slate-400">-</span>
                                            <span class="font-bold text-blue-600">{{ $account['account_number'] }}</span>
                                            <span class="mx-1 text-slate-400">-</span>
                                            <span>a.n. {{ $account['account_holder'] }}</span>
                                            @if ($account['is_primary'])
                                                <span class="ml-1 text-xs font-semibold text-blue-600">(utama)</span>
                                            @endif
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('orders.payment.upload', $order->invoice_number) }}" enctype="multipart/form-data" class="mt-5 space-y-5" onsubmit="window.rcTrack && window.rcTrack('PurchasePending', {source: 'upload_payment'});">
                            @csrf
                            <div>
                                <label for="payment_proof" class="block text-sm font-bold text-slate-700">File Bukti Pembayaran</label>
                                <input id="payment_proof" name="payment_proof" type="file" accept=".jpg,.jpeg,.png,.pdf" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 file:mr-4 file:rounded-xl file:border-0 file:bg-amber-400 file:px-4 file:py-2 file:font-semibold file:text-slate-950 hover:file:bg-amber-300" required>
                                <p class="mt-1.5 text-sm text-slate-500">Format: JPG, JPEG, PNG, atau PDF.</p>
                                @error('payment_proof')
                                    <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_note" class="block text-sm font-bold text-slate-700">Catatan Pembayaran</label>
                                <textarea id="payment_note" name="payment_note" rows="3" placeholder="Contoh: Transfer dari BCA atas nama Khairul Anwar" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:ring-blue-600">{{ old('payment_note', $order->payment_note) }}</textarea>
                                <p class="mt-1.5 text-sm text-slate-500">Opsional. Isi jika ada info tambahan untuk admin.</p>
                                @error('payment_note')
                                    <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="rc-btn-primary w-full px-6 py-4 text-base">
                                Upload Bukti Pembayaran
                            </button>

                            <p class="text-sm leading-6 text-slate-500">Pastikan nominal transfer terlihat jelas, sesuai invoice, dan nama pengirim mudah dikenali.</p>
                        </form>
                    </div>
                </div>

                <aside class="space-y-5">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-lg font-black text-slate-950">Ringkas</h3>
                        <div class="mt-3 space-y-2.5 text-sm">
                            <div class="flex justify-between gap-3"><span class="text-slate-500">Pembeli</span><span class="font-bold text-slate-950 text-right">{{ $order->buyer_name }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-slate-500">Email</span><span class="font-bold text-slate-950 break-all text-right">{{ $order->buyer_email }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-slate-500">Status</span><span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">{{ $statusLabel }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-slate-500">Total</span><span class="font-black text-blue-600 text-right">{{ \App\Support\Money::rupiah($order->price) }}</span></div>
                        </div>

                        @if ($qrisExists && $qrisUrl)
                            <div class="mt-4 border-t border-slate-200 pt-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">QRIS</p>
                                <img src="{{ $qrisUrl }}" alt="QRIS Ruang Cerdas" class="mt-3 w-full rounded-2xl border border-slate-200 bg-white p-2">
                            </div>
                        @endif
                    </div>

                    @if ($waSupportUrl)
                        <div class="rounded-[2rem] border border-green-200 bg-green-50 p-5 shadow-sm">
                            <h3 class="text-lg font-black text-green-900">Butuh Bantuan?</h3>
                            <p class="mt-2 text-sm leading-6 text-green-800">
                                Jika ada kendala upload, hubungi admin lewat WhatsApp.
                            </p>
                            <a href="{{ $waSupportUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                                WhatsApp Support
                            </a>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
