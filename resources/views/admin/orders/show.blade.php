@extends('layouts.admin')

@php
    $title = 'Detail Order';
    $subtitle = 'Invoice ' . ($order->invoice_number ?? '-');
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
                            Detail transaksi dan data pembeli.
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
                            <div class="fw-semibold text-dark">
                                {{ $order->invoice_number }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Status</div>
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
                            <div class="fw-semibold text-dark">
                                {{ $order->buyer_whatsapp ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Tanggal Order</div>
                            <div class="fw-semibold text-dark">
                                {{ $order->created_at?->format('d M Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border mb-0">
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
                                    Slug: {{ $order->product->slug ?? '-' }}
                                </div>
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

                <div class="d-grid gap-2">

                    @if ($order->status === 'payment_uploaded')
                        <form method="POST" action="{{ route('admin.orders.approve', $order) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    class="btn btn-success w-100 rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                                <i data-feather="check-circle" style="width: 15px; height: 15px;"></i>
                                <span>Approve Pembayaran</span>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.orders.reject', $order) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    class="btn btn-danger w-100 rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                                <i data-feather="x-circle" style="width: 15px; height: 15px;"></i>
                                <span>Reject Pembayaran</span>
                            </button>
                        </form>
                    @endif

                    @if ($order->status === 'paid' && $order->download_token)
                        <a href="{{ url('/order/' . $order->invoice_number . '/download/' . $order->download_token) }}"
                           target="_blank"
                           class="btn btn-primary w-100 rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                            <i data-feather="download" style="width: 15px; height: 15px;"></i>
                            <span>Test Download</span>
                        </a>
                    @endif

                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 border border-primary border-opacity-10 bg-primary-subtle rounded-2">
                        <div class="bg-primary rounded-circle widget-size text-center">
                            <i data-feather="download" class="text-white" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>

                    <div>
                        <p class="text-muted fs-13 mb-1">
                            Jumlah Download
                        </p>
                        <h4 class="mb-0">
                            {{ $order->download_count ?? 0 }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection