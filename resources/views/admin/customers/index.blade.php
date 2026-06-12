@extends('layouts.admin')

@php
    $title = 'Customer';
    $subtitle = 'Daftar kontak pembeli dari data order.';
@endphp

@section('content')
<div class="row g-3 mb-3">
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Customer Unik</p>
                <h4 class="mb-0 text-dark">{{ number_format($summary['total_customers'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Pernah Paid</p>
                <h4 class="mb-0 text-success">{{ number_format($summary['customers_has_paid'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Total Order</p>
                <h4 class="mb-0 text-dark">{{ number_format($summary['total_orders'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Total Belanja Paid</p>
                <h4 class="mb-0 text-primary">{{ \App\Support\Money::format($summary['total_paid_revenue'] ?? 0) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Customer Baru 30 Hari</p>
                <h4 class="mb-0 text-info">{{ number_format($summary['new_customers_30_days'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-1">Daftar Kontak Customer</h5>
        <p class="text-muted mb-0 fs-13">Data customer dikelompokkan dari order berdasarkan email atau WhatsApp.</p>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 mb-4">
            <div class="col-lg-4 col-md-6">
                <input type="text"
                       name="q"
                       value="{{ $filters['q'] ?? '' }}"
                       class="form-control"
                       placeholder="Cari nama, email, atau WhatsApp">
            </div>
            <div class="col-lg-3 col-md-6">
                <select name="status" class="form-select">
                    <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Semua</option>
                    <option value="has_paid" @selected(($filters['status'] ?? 'all') === 'has_paid')>Pernah Paid</option>
                    <option value="pending_only" @selected(($filters['status'] ?? 'all') === 'pending_only')>Pending Only</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
            </div>
            <div class="col-lg-2 col-md-6">
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
            </div>
            <div class="col-lg-1 col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-3">
                    Filter
                </button>
            </div>
        </form>

        @if ($customers->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Customer</th>
                            <th style="width: 90px;">Order</th>
                            <th style="width: 80px;">Paid</th>
                            <th style="width: 90px;">Pending</th>
                            <th style="width: 90px;">Rejected</th>
                            <th style="width: 150px;">Belanja Paid</th>
                            <th style="width: 160px;">Order Terakhir</th>
                            <th style="width: 200px;">Produk Terakhir</th>
                            <th style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $customer['name'] }}</div>
                                    <div class="text-muted fs-13">{{ $customer['email'] ?: '-' }}</div>
                                    <div class="text-muted fs-13">{{ $customer['whatsapp'] ?: '-' }}</div>
                                </td>
                                <td>{{ number_format($customer['total_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($customer['total_paid_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($customer['total_pending_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($customer['total_rejected_orders'], 0, ',', '.') }}</td>
                                <td class="fw-semibold">{{ \App\Support\Money::format($customer['total_paid_revenue']) }}</td>
                                <td>
                                    @if ($customer['last_order_at'])
                                        <div>{{ $customer['last_order_at']->format('d M Y') }}</div>
                                        <div class="text-muted fs-13">{{ $customer['last_order_at']->format('H:i') }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-wrap">{{ $customer['last_product_name'] ?: '-' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('admin.orders.index', ['q' => $customer['order_query']]) }}"
                                           class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">
                                            Lihat Order
                                        </a>
                                        @if ($customer['whatsapp_wa'])
                                            <a href="https://wa.me/{{ $customer['whatsapp_wa'] }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="btn btn-sm bg-success-subtle text-success rounded-pill px-3">
                                                WhatsApp
                                            </a>
                                        @endif
                                        @if ($customer['email'])
                                            <a href="mailto:{{ $customer['email'] }}"
                                               class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">
                                                Email
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="text-dark mb-1">Belum ada data customer dari order.</h5>
                <p class="text-muted mb-0">Data customer akan muncul setelah ada order masuk.</p>
            </div>
        @endif
    </div>
</div>
@endsection
