@extends('layouts.admin')

@php
    $title = 'Order Masuk';
    $subtitle = 'Kelola order, bukti pembayaran, dan status pembelian produk digital.';
@endphp

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Daftar Order</h5>
                <p class="text-muted mb-0 fs-13">
                    Semua transaksi pembelian produk digital Ruang Cerdas.
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="card-body">

        @if (($orders ?? collect())->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th style="width: 150px;">Invoice</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th style="width: 120px;">Total</th>
                            <th style="width: 150px;">Status</th>
                            <th style="width: 140px;">Tanggal</th>
                            <th style="width: 100px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <span class="fw-semibold text-dark d-inline-block text-break" style="max-width: 135px;">
                                        {{ $order->invoice_number }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-medium text-dark">
                                        {{ $order->buyer_name }}
                                    </div>

                                    <div class="text-muted fs-13">
                                        {{ $order->buyer_email }}
                                    </div>

                                    @if ($order->buyer_whatsapp)
                                        <div class="text-muted fs-13">
                                            WA: {{ $order->buyer_whatsapp }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="d-inline-block text-wrap" style="max-width: 180px;">
                                        {{ $order->product->name ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ \App\Support\Money::format($order->price ?? 0) }}
                                    </span>
                                </td>

                                <td>
                                    <x-admin.status-badge :status="$order->status" />
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $order->created_at?->format('d M Y') }}
                                    </span>
                                    <div class="fs-13 text-muted">
                                        {{ $order->created_at?->format('H:i') }}
                                    </div>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1 rc-action-btn">
                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (method_exists($orders, 'links'))
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i data-feather="inbox" class="text-muted" style="width: 46px; height: 46px;"></i>
                </div>

                <h5 class="text-dark mb-1">Belum ada order</h5>
                <p class="text-muted mb-0">
                    Order pembelian akan muncul di halaman ini.
                </p>
            </div>
        @endif

    </div>
</div>

@endsection