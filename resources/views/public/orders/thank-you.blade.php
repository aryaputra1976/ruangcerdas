@extends('layouts.public')

@section('title', 'Instruksi Pembayaran - ' . $order->invoice_number)
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
    $paymentNote = $paymentConfig['payment_note'] ?? 'Transfer sesuai nominal invoice agar verifikasi lebih cepat.';
    $qrisStorageExists = filled($qrisImage) && \Illuminate\Support\Facades\Storage::disk('public')->exists($qrisImage);
    $qrisPublicExists = filled($qrisImage) && file_exists(public_path($qrisImage));
    $qrisExists = $qrisStorageExists || $qrisPublicExists;
    $qrisUrl = $qrisStorageExists
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($qrisImage)
        : ($qrisPublicExists ? asset($qrisImage) : null);
    $hasBankInstruction = $bankAccounts->isNotEmpty();
    $hasPaymentInstruction = $hasBankInstruction || $qrisExists;
    $isTryoutOrder = isset($tryoutPackage) && $tryoutPackage;

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
        \App\Models\Order::STATUS_PENDING => 'Selesaikan Pembayaran Anda',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti Pembayaran Menunggu Verifikasi',
        \App\Models\Order::STATUS_PAID => 'Pembayaran Disetujui',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran Ditolak',
        default => 'Status Pembayaran',
    };

    $pageSubtitle = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Transfer sesuai nominal invoice, lalu upload bukti pembayaran agar admin bisa memverifikasi lebih cepat.',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Bukti pembayaran sudah diterima. Tim admin akan melakukan verifikasi.',
        \App\Models\Order::STATUS_PAID => $isTryoutOrder
            ? 'Pembayaran Anda sudah disetujui. Akses tryout premium aktif untuk email pembelian ini.'
            : 'Pembayaran Anda sudah disetujui. Buka Ruang Akses dengan email pembeli dan nomor invoice untuk mengakses produk.',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran ditolak. Silakan periksa alasan dan upload ulang bukti jika diperlukan.',
        default => 'Silakan cek status order Anda secara berkala.',
    };

    $waUrl = \App\Support\WhatsApp::waMeUrl(
        $supportWhatsapp ?? null,
        'Halo Admin Ruang Cerdas, saya butuh bantuan untuk invoice ' . $order->invoice_number . '.'
    );
    $whatsAppAfterUploadUrl = session('whatsapp_after_upload_url');
    $printStatusLabel = match ($order->status) {
        \App\Models\Order::STATUS_PENDING => 'Untuk Pembayaran',
        \App\Models\Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi',
        \App\Models\Order::STATUS_PAID => 'Sudah Lunas',
        \App\Models\Order::STATUS_REJECTED => 'Pembayaran Ditolak',
        default => $statusLabel,
    };
    $printLogoUrl = asset('hando/assets/images/rc/rc_mark.svg');
    $printReference = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $order->invoice_number), -8));
    $tryoutPrimaryLabel = $isTryoutOrder ? 'Mulai Tryout Sekarang' : 'Buka Halaman Tryout';
@endphp

<style>
    .print-only {
        display: none;
    }

    .print-receipt {
        display: none;
    }

    @media print {
        @page {
            margin: 8mm;
        }

        header,
        footer,
        .print-hidden {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        .screen-content {
            display: none !important;
        }

        .print-receipt {
            display: block !important;
            width: 80mm;
            margin: 0 auto;
            color: #0f172a;
            font-family: "Courier New", Courier, monospace;
            font-size: 12px;
            line-height: 1.45;
        }

        body {
            background: #fff !important;
        }

        .print-root {
            max-width: 100% !important;
            padding: 0 !important;
        }

        .print-card {
            border-color: #cbd5e1 !important;
            box-shadow: none !important;
        }

        .print-grid-single {
            display: block !important;
        }

        .print-spacing-tight {
            margin-top: 1rem !important;
        }

        .print-status-chip {
            border: 1px solid #cbd5e1 !important;
            background: #f8fafc !important;
            color: #0f172a !important;
        }
    }
</style>

<section class="bg-slate-50 pt-3 pb-6 md:pt-4 md:pb-8">
    <div class="print-root mx-auto max-w-7xl px-6">
        <div class="print-receipt">
            <div style="text-align:center;">
                <img src="{{ $printLogoUrl }}" alt="Ruang Cerdas" style="width:42px;height:42px;object-fit:contain;margin:0 auto 6px;">
                <div style="font-size:20px;font-weight:700;">RUANG CERDAS</div>
                <div style="font-size:12px;">Struk Pembelian Digital</div>
            </div>

            <div style="border-top:1px dashed #94a3b8;border-bottom:1px dashed #94a3b8;padding:8px 0;margin:10px 0;">
                <table style="width:100%;border-collapse:collapse;font-size:12px;line-height:1.55;">
                    <tr>
                        <td style="width:86px;vertical-align:top;">No. Invoice</td>
                        <td style="width:14px;vertical-align:top;">:</td>
                        <td style="vertical-align:top;">{{ $order->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">Tanggal</td>
                        <td style="vertical-align:top;">:</td>
                        <td style="vertical-align:top;">{{ $order->created_at?->format('d-m-Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">Status</td>
                        <td style="vertical-align:top;">:</td>
                        <td style="vertical-align:top;">{{ strtoupper($printStatusLabel) }}</td>
                    </tr>
                </table>
            </div>

            <div style="margin:10px 0;">
                <table style="width:100%;border-collapse:collapse;font-size:12px;line-height:1.55;">
                    <tr>
                        <td style="width:86px;vertical-align:top;">Produk</td>
                        <td style="width:14px;vertical-align:top;">:</td>
                        <td style="vertical-align:top;">{{ $order->product->name }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">Pembeli</td>
                        <td style="vertical-align:top;">:</td>
                        <td style="vertical-align:top;">{{ $order->buyer_name }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">Email</td>
                        <td style="vertical-align:top;">:</td>
                        <td style="vertical-align:top;word-break:break-all;">{{ $order->buyer_email }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">WhatsApp</td>
                        <td style="vertical-align:top;">:</td>
                        <td style="vertical-align:top;">{{ $order->buyer_whatsapp }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">Metode</td>
                        <td style="vertical-align:top;">:</td>
                        <td style="vertical-align:top;">{{ $hasBankInstruction ? 'Transfer Bank' : ($qrisExists ? 'QRIS' : '-') }}</td>
                    </tr>
                </table>
            </div>

            <div style="border-top:1px dashed #94a3b8;padding-top:8px;margin-top:8px;">
                @if ((float) ($order->discount_amount ?? 0) > 0)
                    <div style="display:flex;justify-content:space-between;gap:8px;">
                        <span>Harga Awal</span>
                        <span>{{ \App\Support\Money::rupiah((float) ($order->original_price ?? $order->price)) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px;">
                        <span>Diskon</span>
                        <span>-{{ \App\Support\Money::rupiah((float) $order->discount_amount) }}</span>
                    </div>
                @endif
                <div style="margin-top:8px;border:1px dashed #94a3b8;padding:8px 10px;text-align:center;">
                    <div style="font-size:11px;letter-spacing:.08em;">TOTAL BAYAR</div>
                    <div style="font-size:24px;font-weight:700;line-height:1.2;margin-top:2px;">{{ \App\Support\Money::rupiah($order->price) }}</div>
                </div>
            </div>

            @if ($hasBankInstruction)
                <div style="border-top:1px dashed #94a3b8;padding-top:8px;margin-top:10px;">
                <div style="font-weight:700;margin-bottom:4px;">TRANSFER KE:</div>
                    @foreach ($bankAccounts as $account)
                        <div style="margin-bottom:8px;padding-bottom:8px;{{ ! $loop->last ? 'border-bottom:1px dashed #cbd5e1;' : '' }}">
                            <table style="width:100%;border-collapse:collapse;font-size:12px;line-height:1.55;">
                                <tr>
                                    <td style="width:86px;vertical-align:top;">Bank</td>
                                    <td style="width:14px;vertical-align:top;">:</td>
                                    <td style="vertical-align:top;">
                                        {{ $account['bank_name'] }}
                                        @if ($account['is_primary'])
                                            <span style="font-size:10px;font-weight:700;">(UTAMA)</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">No. Rek</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top;font-weight:700;letter-spacing:.03em;">{{ $account['account_number'] }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">Atas Nama</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top;">{{ $account['account_holder'] }}</td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($qrisExists && $qrisUrl)
                <div style="border-top:1px dashed #94a3b8;padding-top:8px;margin-top:10px;text-align:center;">
                    <div style="font-weight:700;margin-bottom:6px;">QRIS</div>
                    <img src="{{ $qrisUrl }}" alt="QRIS Ruang Cerdas" style="width:140px;max-width:100%;margin:0 auto;border:1px solid #cbd5e1;border-radius:10px;">
                </div>
            @endif

            <div style="border-top:1px dashed #94a3b8;padding-top:8px;margin-top:10px;">
                <div>Transfer sesuai nominal invoice.</div>
                <div>Upload bukti pembayaran setelah transfer.</div>
                @if ($isTryoutOrder)
                    <div>Akses tryout aktif setelah disetujui admin.</div>
                @else
                    <div>Akses produk melalui Ruang Akses.</div>
                @endif
            </div>

            <div style="border-top:1px dashed #94a3b8;margin-top:10px;padding-top:8px;text-align:center;">
                <div>Simpan invoice ini untuk cek status order</div>
                <div>dan akses produk digital Anda.</div>
                <div style="margin-top:6px;">Ref Admin: {{ $printReference }}</div>
                <div style="margin-top:6px;">www.ruangcerdas.id</div>
            </div>
        </div>

        <div class="screen-content">

        @if (session('success'))
            <div class="print-hidden mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($whatsAppAfterUploadUrl)
            <div class="print-hidden mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <div class="font-semibold">Pemberitahuan ke admin siap dikirim.</div>
                <div class="mt-1">Jika WhatsApp belum terbuka otomatis, gunakan tombol di bawah untuk mengirim notifikasi upload bukti pembayaran.</div>
                <a href="{{ $whatsAppAfterUploadUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                    Kirim Pemberitahuan ke WhatsApp Admin
                </a>
            </div>
        @endif

        <div class="mb-4 print-hidden">
            <div class="mb-3 flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">
                    {{ $isTryoutOrder ? 'Akses Tryout' : 'Ruang Akses' }}
                </span>
                <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-700">
                    {{ $isTryoutOrder ? 'Produk Tryout' : 'Produk Digital' }}
                </span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-4xl">{{ $pageTitle }}</h1>
            <p class="mt-2 max-w-3xl text-slate-600">{{ $pageSubtitle }}</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px] print-grid-single">
            <div class="space-y-5">
                <div class="print-card rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Invoice</p>
                            <h2 class="mt-2 break-all text-2xl font-black text-slate-950 md:text-3xl">{{ $order->invoice_number }}</h2>
                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-600">
                                <span><span class="font-semibold text-slate-900">Produk:</span> {{ $order->product->name }}</span>
                                <span><span class="font-semibold text-slate-900">Total:</span> <span class="font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</span></span>
                                @if ($hasBankInstruction)
                                    <span><span class="font-semibold text-slate-900">Metode:</span> Transfer Bank</span>
                                @elseif ($qrisExists)
                                    <span><span class="font-semibold text-slate-900">Metode:</span> QRIS</span>
                                @endif
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-blue-700">
                                    {{ $isTryoutOrder ? 'Produk Tryout' : 'Produk Digital' }}
                                </span>
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-slate-700">
                                    {{ $isTryoutOrder ? 'Akses via Halaman Tryout' : 'Akses via Ruang Akses' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex rounded-full px-4 py-2 text-sm font-bold uppercase {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                            <button type="button" onclick="window.print()" class="print-hidden inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600">
                                Cetak Invoice
                            </button>
                        </div>
                    </div>

                    <div class="print-only print-spacing-tight mt-6 grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm text-slate-500">Nama Pembeli</p>
                            <p class="mt-1 font-bold text-slate-950">{{ $order->buyer_name }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm text-slate-500">WhatsApp</p>
                            <p class="mt-1 font-bold text-slate-950">{{ $order->buyer_whatsapp }}</p>
                        </div>
                    </div>

                    @if ($hasBankInstruction)
                        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <div class="flex flex-col gap-1 md:flex-row md:items-end md:justify-between">
                                <div>
                                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Rekening Tujuan</p>
                                    <p class="mt-1 text-sm text-slate-600">Pilih satu rekening lalu transfer sesuai nominal invoice.</p>
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
                    @else
                        <div class="mt-5 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800">
                            Instruksi pembayaran belum tersedia. Silakan hubungi admin Ruang Cerdas.
                        </div>
                    @endif

                    @if ($order->status !== \App\Models\Order::STATUS_PAID && ! $order->payment_proof_path)
                        <div class="print-hidden mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                            <h3 class="text-lg font-black text-amber-950">
                                Sudah transfer?
                                <span class="ml-1 align-middle text-sm font-semibold text-amber-800">Setelah transfer, upload bukti pembayaran agar admin bisa segera memverifikasi.</span>
                            </h3>
                            <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="rc-btn-primary mt-4 w-full px-6 py-3 text-sm sm:w-auto">
                                Saya Sudah Bayar, Upload Bukti Pembayaran
                            </a>
                        </div>
                    @endif

                    @if ($hasPaymentInstruction && !empty($paymentNote))
                        <div class="mt-4 text-sm leading-6 text-slate-600">
                            {{ $paymentNote }}
                        </div>
                    @endif
                </div>

                <div class="print-card rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                    <h2 class="text-xl font-black text-slate-950">Langkah Berikutnya</h2>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-6 text-slate-700">
                        @if ($order->status === \App\Models\Order::STATUS_PENDING)
                            Transfer sesuai nominal invoice, lalu upload bukti pembayaran. Setelah diverifikasi admin, akses produk akan dibuka.
                        @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                            Bukti pembayaran sudah diterima. Tunggu verifikasi admin, lalu lanjutkan akses dari halaman ini.
                        @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                            @if ($isTryoutOrder)
                                Pembayaran sudah disetujui. Masuk ke halaman tryout lalu mulai dengan email pembelian yang sama.
                            @else
                                Pembayaran sudah disetujui. Buka Ruang Akses dengan email pembeli dan nomor invoice ini.
                            @endif
                        @elseif ($order->status === \App\Models\Order::STATUS_REJECTED)
                            Periksa alasan penolakan pembayaran, lalu upload ulang bukti yang lebih jelas jika masih ingin melanjutkan order ini.
                        @endif
                    </div>

                    @if ($order->status === \App\Models\Order::STATUS_REJECTED && !empty($order->rejection_reason))
                        <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm leading-6 text-red-700">
                            Alasan penolakan: {{ $order->rejection_reason }}
                        </div>
                    @endif
                </div>

                @if ($waUrl)
                    <div class="print-hidden rounded-[2rem] border border-green-200 bg-green-50 p-6 shadow-sm md:p-8">
                        <h2 class="text-2xl font-black text-green-900">Butuh Bantuan Pembelian?</h2>
                        <p class="mt-3 text-sm leading-7 text-green-800">
                            Anda tetap dapat menyelesaikan pembelian secara mandiri. Jika ada kendala, tim support siap membantu melalui WhatsApp.
                        </p>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-bold text-white hover:bg-green-700">
                            WhatsApp Support
                        </a>
                    </div>
                @endif
            </div>

            <aside class="space-y-5">
                <div class="print-card rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ringkasan Invoice</p>
                    <div class="mt-3 space-y-2.5 text-sm">
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Invoice</span><span class="font-bold text-slate-950 break-all text-right">{{ $order->invoice_number }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Produk</span><span class="font-bold text-slate-950 text-right">{{ $order->product->name }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Status</span><span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $statusClass }}">{{ $statusLabel }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Tanggal</span><span class="font-bold text-slate-950">{{ $order->created_at?->format('d M Y H:i') }}</span></div>
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        @if ((float) ($order->discount_amount ?? 0) > 0)
                            <div class="mb-2 flex justify-between gap-3 text-sm"><span class="text-slate-500">Harga Awal</span><span class="font-bold text-slate-700">{{ \App\Support\Money::rupiah((float) ($order->original_price ?? $order->price)) }}</span></div>
                            <div class="mb-2 flex justify-between gap-3 text-sm"><span class="text-slate-500">Kode Kupon</span><span class="font-bold text-emerald-700">{{ $order->coupon_code ?: '-' }}</span></div>
                            <div class="mb-2 flex justify-between gap-3 text-sm"><span class="text-slate-500">Diskon</span><span class="font-bold text-emerald-700">-{{ \App\Support\Money::rupiah((float) $order->discount_amount) }}</span></div>
                        @endif
                        <div class="flex justify-between gap-3"><span class="text-slate-600">Total Bayar</span><span class="text-2xl font-black text-blue-600">{{ \App\Support\Money::rupiah($order->price) }}</span></div>
                    </div>

                    <button type="button" onclick="window.print()" class="print-hidden rc-btn-neutral mt-4 w-full px-5 py-3 text-sm">
                        Cetak Invoice
                    </button>
                </div>

                @if ($isTryoutOrder)
                    <div class="print-card rounded-[2rem] border border-blue-100 bg-blue-50 p-5 shadow-sm">
                        <h3 class="text-lg font-black text-blue-950">Akses Tryout</h3>
                        <div class="mt-3 space-y-2 text-sm leading-6 text-blue-900">
                            <p>Buka halaman {{ $tryoutPackage->tryout_type_label }} lalu mulai dengan email pembelian yang sama.</p>
                            <p>Paket aktif: <span class="font-bold">{{ $tryoutPackage->title }}</span></p>
                        </div>
                        <div class="mt-4 flex flex-col gap-3">
                            <a href="{{ route($tryoutPackage->listingRouteName()) }}" class="rc-btn-secondary px-5 py-3 text-sm">
                                {{ $tryoutPrimaryLabel }}
                            </a>
                            <a href="{{ route('public.tryouts.packages.start', ['tryoutType' => $tryoutPackage->routeSegment(), 'tryoutPackage' => $tryoutPackage]) }}" class="rc-btn-neutral px-5 py-3 text-sm">
                                Lihat Halaman Paket
                            </a>
                        </div>
                    </div>
                @endif

                @if ($qrisExists && $qrisUrl)
                    <div class="print-hidden rounded-[2rem] border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
                        <h3 class="text-lg font-black text-emerald-950">QRIS</h3>
                        <div class="mt-4 rounded-2xl bg-white p-4 text-center">
                            <img src="{{ $qrisUrl }}" alt="QRIS Ruang Cerdas" class="mx-auto w-full max-w-[240px] rounded-2xl border border-slate-200">
                        </div>
                    </div>
                @endif
            </aside>
        </div>

        <div class="print-only mt-8 border-t border-slate-300 pt-4 text-xs leading-6 text-slate-500">
            Invoice ini dicetak dari sistem Ruang Cerdas. Simpan nomor invoice untuk keperluan verifikasi pembayaran, pengecekan status order, dan akses produk digital.
            <span class="ml-2 inline-block font-semibold text-slate-700">Halaman 1</span>
        </div>

        <div class="print-hidden mt-8 flex flex-col gap-3 sm:flex-row">
            @if ($order->status === \App\Models\Order::STATUS_PENDING || $order->status === \App\Models\Order::STATUS_REJECTED)
                <a href="{{ route('orders.payment.form', $order->invoice_number) }}" class="rc-btn-primary flex-1 px-6 py-4 text-center">
                    Upload Bukti Pembayaran
                </a>
            @elseif ($order->status === \App\Models\Order::STATUS_PAYMENT_UPLOADED)
                <a href="{{ route('public.order-tracking.index') }}" class="rc-btn-secondary flex-1 px-6 py-4 text-center">
                    Status Order
                </a>
            @elseif ($order->status === \App\Models\Order::STATUS_PAID && ! $isTryoutOrder)
                <a href="{{ route('public.download-room.index') }}" class="rc-btn-success flex-1 px-6 py-4 text-center">
                    Ruang Akses
                </a>
            @elseif ($order->status === \App\Models\Order::STATUS_PAID && $isTryoutOrder)
                <a href="{{ route($tryoutPackage->listingRouteName()) }}" class="rc-btn-success flex-1 px-6 py-4 text-center">
                    {{ $tryoutPrimaryLabel }}
                </a>
            @endif
            <a href="{{ route('products.index') }}" class="rc-btn-neutral flex-1 px-6 py-4 text-center">
                Kembali ke Produk
            </a>
        </div>
        </div>
    </div>
</section>
@endsection

@if ($whatsAppAfterUploadUrl)
    @push('scripts')
        <script>
            window.addEventListener('load', function () {
                const whatsappUrl = @json($whatsAppAfterUploadUrl);

                if (!whatsappUrl || sessionStorage.getItem('rc-whatsapp-upload-notified') === whatsappUrl) {
                    return;
                }

                sessionStorage.setItem('rc-whatsapp-upload-notified', whatsappUrl);
                window.open(whatsappUrl, '_blank', 'noopener');
            });
        </script>
    @endpush
@endif
