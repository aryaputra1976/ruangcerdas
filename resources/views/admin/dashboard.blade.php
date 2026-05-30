@extends('layouts.admin')

@php
    $title = 'Dashboard';
    $subtitle = 'Ringkasan performa penjualan produk digital Ruang Cerdas.';
@endphp

@section('content')

    {{-- Summary Cards --}}
    <div class="row">

        <x-admin.stat-card
            title="Total Produk"
            :value="$total_products ?? 0"
            description="Semua produk"
            icon="package"
            color="primary"
        />

        <x-admin.stat-card
            title="Produk Aktif"
            :value="$active_products ?? 0"
            description="Produk dijual"
            icon="check-circle"
            color="success"
        />

        <x-admin.stat-card
            title="Order Baru"
            :value="$new_orders ?? 0"
            description="Order pending"
            icon="shopping-cart"
            color="warning"
            :url="route('admin.orders.index')"
        />

        <x-admin.stat-card
            title="Menunggu Verifikasi"
            :value="$waiting_verification ?? 0"
            description="Bukti bayar masuk"
            icon="clock"
            color="danger"
            :url="route('admin.orders.index')"
        />

        <x-admin.stat-card
            title="Omzet"
            :value="\App\Support\Money::format($revenue ?? 0)"
            description="Total order paid"
            icon="dollar-sign"
            color="success"
        />

    </div>

    {{-- Second Row --}}
    <div class="row">

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">
                            Order Terbaru
                        </h5>

                        <div class="ms-auto">
                            <a href="{{ route('admin.orders.index') }}"
                            class="btn btn-sm btn-primary rounded-pill px-3">
                                <i data-feather="list" class="me-1" style="width: 14px; height: 14px;"></i>
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (($latestOrders ?? collect())->count())
                        <div class="table-responsive table-card">
                            <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                <thead class="text-muted table-light">
                                    <tr>
                                        <th style="width: 150px;">Invoice</th>
                                        <th>Pembeli</th>
                                        <th>Produk</th>
                                        <th style="width: 110px;">Total</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 130px;">Tanggal</th>
                                        <th style="width: 90px;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($latestOrders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark d-inline-block text-break" style="max-width: 130px;">
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
                                            </td>

                                            <td>
                                                <span class="d-inline-block text-wrap" style="max-width: 140px;">
                                                    {{ $order->product->name ?? '-' }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="fw-semibold">
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
                    @else
                        <div class="text-center py-5">
                            <div class="mb-2">
                                <i data-feather="inbox" class="text-muted" style="width: 40px; height: 40px;"></i>
                            </div>
                            <h6 class="text-muted mb-0">
                                Belum ada order terbaru.
                            </h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">
                            Menunggu Verifikasi
                        </h5>
                    </div>
                </div>

                <div class="card-body">
                    @if (($waitingOrders ?? collect())->count())
                        <ul class="list-group list-group-flush list-group-no-gutters">
                            @foreach ($waitingOrders as $order)
                                <li class="list-group-item px-0">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="avatar bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                                                <i data-feather="clock" style="width: 18px; height: 18px;"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between gap-2">
                                                <div>
                                                    <h6 class="mb-1 text-dark fs-15">
                                                        {{ $order->buyer_name }}
                                                    </h6>

                                                    <div class="fs-13 text-muted">
                                                        {{ $order->invoice_number }}
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <div class="fw-semibold text-dark fs-14">
                                                        {{ \App\Support\Money::format($order->price ?? 0) }}
                                                    </div>

                                                    <a href="{{ route('admin.orders.show', $order) }}"
                                                       class="fs-13 text-primary">
                                                        Verifikasi
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-2">
                                <i data-feather="check-circle" class="text-success" style="width: 40px; height: 40px;"></i>
                            </div>
                            <h6 class="text-muted mb-0">
                                Tidak ada order yang menunggu verifikasi.
                            </h6>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="p-2 border border-primary border-opacity-10 bg-primary-subtle rounded-2 me-3">
                            <div class="bg-primary rounded-circle widget-size text-center">
                                <i data-feather="download" class="text-white" style="width: 18px; height: 18px;"></i>
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-muted fs-13">
                                Total Download
                            </p>
                            <h4 class="mb-0 text-dark">
                                {{ $downloads ?? 0 }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection