@extends('layouts.admin')

@php
    $title = 'Laporan Order';
    $subtitle = 'Lihat dan filter data order berdasarkan tanggal, status, dan produk.';
@endphp

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-1">Filter Laporan</h5>
        <p class="text-muted mb-0 fs-13">Gunakan filter untuk mempermudah analisis data order.</p>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
            <div class="col-xl-3 col-md-6">
                <label for="from" class="form-label">Tanggal Mulai</label>
                <input type="date" id="from" name="from" value="{{ $filters['from'] }}" class="form-control">
            </div>

            <div class="col-xl-3 col-md-6">
                <label for="to" class="form-label">Tanggal Akhir</label>
                <input type="date" id="to" name="to" value="{{ $filters['to'] }}" class="form-control">
            </div>

            <div class="col-xl-3 col-md-6">
                <label for="status" class="form-label">Status Order</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>
                            {{ ucfirst(str_replace('_', ' ', $statusOption)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-3 col-md-6">
                <label for="product_id" class="form-label">Produk</label>
                <select id="product_id" name="product_id" class="form-select">
                    <option value="">Semua Produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected((string) $filters['product_id'] === (string) $product->id)>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 d-flex align-items-center gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="filter" style="width: 14px; height: 14px;"></i>
                    <span>Terapkan Filter</span>
                </button>

                <a href="{{ route('admin.reports.index') }}"
                   class="btn btn-light rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-1">Data Order</h5>
        <p class="text-muted mb-0 fs-13">Menampilkan order sesuai filter yang dipilih.</p>
    </div>
    <div class="card-body">
        @if ($orders->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Invoice</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Tanggal Order</th>
                            <th>Tanggal Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $order->invoice_number }}</td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $order->buyer_name }}</div>
                                    <div class="text-muted fs-13">{{ $order->buyer_email }}</div>
                                </td>
                                <td>{{ $order->product?->name ?? '-' }}</td>
                                <td class="fw-semibold">{{ \App\Support\Money::format($order->price ?? 0) }}</td>
                                <td><x-admin.status-badge :status="$order->status" /></td>
                                <td>
                                    <div>{{ $order->created_at?->format('d M Y') }}</div>
                                    <div class="fs-13 text-muted">{{ $order->created_at?->format('H:i') }}</div>
                                </td>
                                <td>
                                    @if ($order->paid_at)
                                        <div>{{ $order->paid_at->format('d M Y') }}</div>
                                        <div class="fs-13 text-muted">{{ $order->paid_at->format('H:i') }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i data-feather="inbox" class="text-muted mb-2" style="width: 44px; height: 44px;"></i>
                <h5 class="text-dark mb-1">Data tidak ditemukan</h5>
                <p class="text-muted mb-0">Tidak ada order yang sesuai dengan filter saat ini.</p>
            </div>
        @endif
    </div>
</div>

@endsection
