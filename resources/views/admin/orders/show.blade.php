@extends('layouts.admin')

@php
    $title = 'Detail Order';
    $subtitle = 'Invoice ' . ($order->invoice_number ?? '-');

    $maxDownloadCount = (int) config('ruangcerdas.download.max_count', 5);
    $downloadCount = (int) ($order->download_count ?? 0);

    $remainingDownload = $maxDownloadCount > 0
        ? max($maxDownloadCount - $downloadCount, 0)
        : null;

    $isDownloadExpired = $order->download_expires_at
        ? $order->download_expires_at->isPast()
        : false;

    $downloadUrl = ($order->status === 'paid' && $order->download_token)
        ? url('/order/' . $order->invoice_number . '/download/' . $order->download_token)
        : null;
@endphp

@section('content')

<div class="row">

    <div class="col-xl-8">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h5 class="card-title mb-1">
                            Informasi Order
                        </h5>

                        <p class="text-muted mb-0 fs-13">
                            Detail transaksi, data pembeli, produk, dan status pembayaran.
                        </p>
                    </div>

                    <a href="{{ route('admin.orders.index') }}"
                       class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="card-body">

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Invoice</div>
                            <div class="fw-semibold text-dark text-break">
                                {{ $order->invoice_number }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Status Order</div>
                            <x-admin.status-badge :status="$order->status" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Nama Pembeli</div>
                            <div class="fw-semibold text-dark">
                                {{ $order->buyer_name }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Email</div>
                            <div class="fw-semibold text-dark text-break">
                                {{ $order->buyer_email }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">WhatsApp</div>

                            @if ($order->buyer_whatsapp)
                                <div class="fw-semibold text-dark">
                                    {{ $order->buyer_whatsapp }}
                                </div>
                            @else
                                <div class="text-muted">
                                    -
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Tanggal Order</div>
                            <div class="fw-semibold text-dark">
                                {{ $order->created_at?->format('d M Y H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card border mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Produk Dibeli</h6>
                    </div>

                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                            <div>
                                <h5 class="mb-1 text-dark">
                                    {{ $order->product->name ?? '-' }}
                                </h5>

                                <div class="text-muted fs-13">
                                    Kategori:
                                    {{ $order->product?->category?->name ?? '-' }}
                                </div>

                                <div class="text-muted fs-13">
                                    Slug:
                                    {{ $order->product->slug ?? '-' }}
                                </div>

                                @if ($order->product?->digital_file_path)
                                    <div class="mt-2">
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            File digital tersedia
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill">
                                            File digital belum tersedia
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="text-end">
                                <div class="text-muted fs-13 mb-1">Harga</div>
                                <h4 class="mb-0 text-dark">
                                    {{ \App\Support\Money::format($order->price ?? 0) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($order->status === 'rejected')
                    <div class="alert alert-danger mb-0">
                        <div class="fw-semibold mb-1">
                            Order ini ditolak.
                        </div>

                        <div>
                            Alasan:
                            {{ $order->rejection_reason ?: 'Tidak ada alasan yang dicatat.' }}
                        </div>

                        @if ($order->rejected_at)
                            <div class="fs-13 mt-1">
                                Ditolak pada:
                                {{ $order->rejected_at->format('d M Y H:i') }}
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Informasi Download
                </h5>
            </div>

            <div class="card-body">

                @if ($order->status === 'paid')
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted fs-13 mb-1">Download Count</div>
                                <div class="fw-semibold text-dark">
                                    {{ $downloadCount }}x
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted fs-13 mb-1">Sisa Download</div>
                                <div class="fw-semibold text-dark">
                                    @if ($maxDownloadCount > 0)
                                        {{ $remainingDownload }} dari {{ $maxDownloadCount }}
                                    @else
                                        Tanpa batas
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted fs-13 mb-1">Expired Link</div>

                                @if ($order->download_expires_at)
                                    <div class="fw-semibold {{ $isDownloadExpired ? 'text-danger' : 'text-dark' }}">
                                        {{ $order->download_expires_at->format('d M Y H:i') }}
                                    </div>

                                    @if ($isDownloadExpired)
                                        <div class="fs-13 text-danger mt-1">
                                            Link sudah expired.
                                        </div>
                                    @else
                                        <div class="fs-13 text-muted mt-1">
                                            Link masih aktif.
                                        </div>
                                    @endif
                                @else
                                    <div class="fw-semibold text-dark">
                                        Tidak ada expired
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted fs-13 mb-1">Token Download</div>

                                @if ($order->download_token)
                                    <div class="text-break fs-13">
                                        {{ $order->download_token }}
                                    </div>
                                @else
                                    <div class="text-muted">
                                        -
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($downloadUrl)
                            <div class="col-12">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="text-muted fs-13 mb-1">Link Download</div>

                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               value="{{ $downloadUrl }}"
                                               readonly>

                                        <a href="{{ $downloadUrl }}"
                                           target="_blank"
                                           class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                                            <span>Test Download</span>
                                        </a>
                                    </div>

                                    <div class="fs-13 text-muted mt-2">
                                        Link ini hanya aktif jika order masih paid, token valid, belum expired, dan belum melewati batas download.
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        Link download belum aktif karena order belum berstatus paid.
                    </div>
                @endif

            </div>
        </div>

    </div>

    <div class="col-xl-4">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Pembayaran
                </h5>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <div class="text-muted fs-13 mb-1">Metode Pembayaran</div>
                    <div class="fw-semibold text-dark">
                        {{ strtoupper($order->payment_method ?? 'manual') }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted fs-13 mb-1">Waktu Upload Bukti</div>
                    <div class="fw-semibold text-dark">
                        {{ $order->payment_uploaded_at?->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted fs-13 mb-1">Waktu Dibayar</div>
                    <div class="fw-semibold text-dark">
                        {{ $order->paid_at?->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted fs-13 mb-1">Waktu Approved</div>
                    <div class="fw-semibold text-dark">
                        {{ $order->approved_at?->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>

                @if ($order->approver)
                    <div class="mb-3">
                        <div class="text-muted fs-13 mb-1">Approved Oleh</div>
                        <div class="fw-semibold text-dark">
                            {{ $order->approver->name }}
                        </div>
                    </div>
                @endif

                @if ($order->payment_note)
                    <div class="mb-3">
                        <div class="text-muted fs-13 mb-1">Catatan Pembeli</div>
                        <div class="border rounded-3 p-3 bg-light">
                            {{ $order->payment_note }}
                        </div>
                    </div>
                @endif

                @if ($order->payment_proof_path)
                    <div class="mb-3">
                        <div class="text-muted fs-13 mb-2">Bukti Pembayaran</div>

                        <a href="{{ asset('storage/' . $order->payment_proof_path) }}"
                           target="_blank"
                           class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <i data-feather="image" style="width: 14px; height: 14px;"></i>
                            <span>Lihat Bukti</span>
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning mb-3">
                        Pembeli belum mengupload bukti pembayaran.
                    </div>
                @endif

                <hr>

                @if ($order->status === 'payment_uploaded')
                    <div class="alert alert-info">
                        Bukti pembayaran sudah diupload. Silakan periksa sebelum approve.
                    </div>

                    <div class="d-grid gap-2">
                        <form method="POST"
                              action="{{ route('admin.orders.approve', $order) }}"
                              onsubmit="return confirm('Approve pembayaran order ini? Link download akan aktif.');">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    class="btn btn-success w-100 rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                                <i data-feather="check-circle" style="width: 15px; height: 15px;"></i>
                                <span>Approve Pembayaran</span>
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.orders.reject', $order) }}"
                              onsubmit="return confirm('Reject pembayaran order ini?');">
                            @csrf
                            @method('PATCH')

                            <div class="mb-2">
                                <textarea name="rejection_reason"
                                          rows="3"
                                          class="form-control"
                                          placeholder="Alasan penolakan, opsional">{{ old('rejection_reason', $order->rejection_reason) }}</textarea>

                                @error('rejection_reason')
                                    <div class="text-danger fs-13 mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit"
                                    class="btn btn-danger w-100 rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                                <i data-feather="x-circle" style="width: 15px; height: 15px;"></i>
                                <span>Reject Pembayaran</span>
                            </button>
                        </form>
                    </div>
                @elseif ($order->status === 'pending')
                    <div class="alert alert-warning mb-0">
                        Order masih pending. Pembeli belum mengupload bukti pembayaran.
                    </div>
                @elseif ($order->status === 'paid')
                    <div class="alert alert-success mb-0">
                        Order sudah paid. Link download sudah aktif selama belum expired dan belum melewati batas download.
                    </div>
                @elseif ($order->status === 'rejected')
                    <div class="alert alert-danger mb-0">
                        Order sudah ditolak.
                    </div>
                @else
                    <div class="alert alert-secondary mb-0">
                        Status order saat ini:
                        <strong>{{ $order->status }}</strong>
                    </div>
                @endif

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 border border-primary border-opacity-10 bg-primary-subtle rounded-2">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width: 38px; height: 38px;">
                            <i data-feather="download" class="text-white" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>

                    <div>
                        <p class="text-muted fs-13 mb-1">
                            Jumlah Download
                        </p>

                        <h4 class="mb-0">
                            {{ $downloadCount }}x
                        </h4>

                        <div class="text-muted fs-13">
                            @if ($maxDownloadCount > 0)
                                Sisa {{ $remainingDownload }} dari {{ $maxDownloadCount }}
                            @else
                                Tanpa batas download
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection