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

    $downloadTokenStatus = filled($order->download_token) ? 'Tersedia' : 'Tidak tersedia';
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

                                <div class="text-muted fs-12 mt-1">
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
                                @if ((float) ($order->discount_amount ?? 0) > 0)
                                    <div class="text-muted fs-13">
                                        Asli: {{ \App\Support\Money::format((float) ($order->original_price ?? 0)) }}
                                    </div>
                                    <div class="text-success fs-13">
                                        Kupon {{ $order->coupon_code }} -{{ \App\Support\Money::format((float) $order->discount_amount) }}
                                    </div>
                                @endif

                                <h4 class="mb-0 text-dark mt-1">
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
                                <div class="fw-semibold text-dark">
                                    {{ $downloadTokenStatus }}
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        Akses download belum aktif karena order belum berstatus paid.
                    </div>
                @endif

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Catatan Internal</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.notes.store', $order) }}" class="mb-4">
                    @csrf

                    <div class="mb-2">
                        <label class="form-label">Tambah Catatan</label>
                        <textarea name="note"
                                  rows="3"
                                  class="form-control @error('note') is-invalid @enderror"
                                  placeholder="Catatan untuk verifikasi, follow-up, atau administrasi internal...">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_pinned_new_note" name="is_pinned" value="1" @checked(old('is_pinned'))>
                            <label class="form-check-label" for="is_pinned_new_note">Pin catatan</label>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-3">
                            Simpan Catatan
                        </button>
                    </div>
                </form>

                @if (filled($order->admin_notes))
                    <div class="alert alert-light border mb-4">
                        <div class="fw-semibold mb-1">Catatan lama</div>
                        <div class="text-muted">{{ $order->admin_notes }}</div>
                    </div>
                @endif

                @if ($order->notes->count())
                    <div class="d-flex flex-column gap-3">
                        @foreach ($order->notes as $note)
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ $note->user?->name ?? 'Admin' }}
                                        </div>
                                        <div class="text-muted fs-13">
                                            {{ $note->created_at?->format('d M Y H:i') ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if ($note->is_pinned)
                                            <span class="badge bg-warning-subtle text-warning rounded-pill">Pinned</span>
                                        @endif
                                    </div>
                                </div>

                                <form method="POST"
                                      action="{{ route('admin.orders.notes.update', [$order, $note]) }}"
                                      class="mb-2">
                                    @csrf
                                    @method('PATCH')

                                    <div class="mb-2">
                                        <textarea name="note"
                                                  rows="3"
                                                  class="form-control">{{ old('note_'.$note->id, $note->note) }}</textarea>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="is_pinned_note_{{ $note->id }}"
                                                   name="is_pinned"
                                                   value="1"
                                                   @checked(old('is_pinned_'.$note->id, $note->is_pinned))>
                                            <label class="form-check-label" for="is_pinned_note_{{ $note->id }}">
                                                Pin catatan
                                            </label>
                                        </div>

                                        <button type="submit" class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">
                                            Update
                                        </button>
                                    </div>
                                </form>

                                <form method="POST"
                                      action="{{ route('admin.orders.notes.destroy', [$order, $note]) }}"
                                      onsubmit="return confirm('Hapus catatan internal ini?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">Belum ada catatan internal untuk order ini.</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Audit Trail Order</h5>
            </div>
            <div class="card-body">
                @if ($order->auditTrails->count())
                    <div class="d-flex flex-column gap-3">
                        @foreach ($order->auditTrails as $trail)
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-primary-subtle text-primary">{{ $trail->action }}</span>
                                        @if ($trail->from_status || $trail->to_status)
                                            <span class="fs-13 text-muted">
                                                {{ $trail->from_status ?? '-' }} -> <span class="fw-semibold text-dark">{{ $trail->to_status ?? '-' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted fs-13">
                                        {{ $trail->created_at?->format('d M Y H:i:s') ?? '-' }}
                                    </div>
                                </div>

                                <div class="text-dark mb-2">
                                    {{ $trail->description ?? '-' }}
                                </div>

                                <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                                    <div class="fs-13">
                                        @if ($trail->user)
                                            <div class="fw-medium text-dark">{{ $trail->user->name }}</div>
                                            <div class="text-muted">{{ $trail->user->email }}</div>
                                        @else
                                            <div class="text-muted">System / Public</div>
                                        @endif
                                    </div>
                                    <div class="text-muted fs-12">
                                        IP: {{ $trail->ip_address ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">Belum ada audit trail untuk order ini.</div>
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

                        <a href="{{ route('admin.orders.payment-proof', $order) }}"
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
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="fw-semibold text-dark mb-2">Checklist Verifikasi Sebelum Approve</div>
                        <div class="row g-2 fs-13">
                            <div class="col-md-6"><span class="text-muted">Invoice:</span> <span class="fw-semibold text-dark">{{ $order->invoice_number }}</span></div>
                            <div class="col-md-6"><span class="text-muted">Pembeli:</span> <span class="fw-semibold text-dark">{{ $order->buyer_name }}</span></div>
                            <div class="col-md-6"><span class="text-muted">Email:</span> <span class="fw-semibold text-dark text-break">{{ $order->buyer_email }}</span></div>
                            <div class="col-md-6"><span class="text-muted">Total Bayar:</span> <span class="fw-semibold text-dark">{{ \App\Support\Money::format($order->price ?? 0) }}</span></div>
                            <div class="col-md-6">
                                <span class="text-muted">Bukti Pembayaran:</span>
                                @if ($order->payment_proof_path)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Tersedia</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Belum tersedia</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">File Produk:</span>
                                @if ($order->product?->privateFileExists())
                                    <span class="badge bg-success-subtle text-success rounded-pill">Siap download</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill">Belum tersedia</span>
                                @endif
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0 py-2 px-3 fs-13">
                            Approve akan mengaktifkan akses download dan mengirim email panduan Ruang Akses ke pembeli.
                        </div>
                    </div>

                    <div class="alert alert-info">
                        Bukti pembayaran sudah diupload. Silakan periksa sebelum approve.
                    </div>

                    <div class="d-grid gap-2">
                        <form method="POST"
                              action="{{ route('admin.orders.approve', $order) }}"
                              onsubmit="return confirm('Approve pembayaran invoice {{ $order->invoice_number }}? Akses download akan aktif dan email panduan Ruang Akses akan dikirim ke {{ $order->buyer_email }}.');">
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
                                <label for="rejection_reason" class="form-label mb-1">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason"
                                          id="rejection_reason"
                                          rows="3"
                                          class="form-control"
                                          placeholder="Alasan penolakan wajib diisi">{{ old('rejection_reason', $order->rejection_reason) }}</textarea>
                                <div class="form-text">
                                    Alasan akan membantu pembeli memperbaiki bukti pembayaran.
                                </div>

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
                    <div class="alert alert-success mb-3">
                        Order sudah paid. Akses download aktif selama belum expired dan belum melewati batas download.
                    </div>

                    @if ($order->buyer_email && $order->product && $order->product->privateFileExists())
                        <form method="POST"
                              action="{{ route('admin.orders.resend-download-link', $order) }}"
                              onsubmit="return confirm('Kirim ulang email panduan Ruang Akses ke pembeli?');">
                            @csrf

                            <button type="submit"
                                    class="btn btn-success w-100 rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                                <i data-feather="send" style="width: 15px; height: 15px;"></i>
                                <span>Kirim Ulang Email Ruang Akses</span>
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning mb-0">
                            Email Ruang Akses belum dapat dikirim ulang karena email pembeli atau file produk belum tersedia.
                        </div>
                    @endif
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
