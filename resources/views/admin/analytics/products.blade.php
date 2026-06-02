@extends('layouts.admin')

@php
    $title = 'Analytics Produk';
    $subtitle = 'Funnel sederhana dari view produk sampai order paid.';
@endphp

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-1">Filter Analytics</h5>
        <p class="text-muted mb-0 fs-13">Periode default 30 hari terakhir.</p>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.analytics.products.index') }}" class="row g-3">
            <div class="col-xl-2 col-md-4">
                <label for="from" class="form-label">Tanggal Mulai</label>
                <input type="date" id="from" name="from" value="{{ $filters['from'] }}" class="form-control">
            </div>

            <div class="col-xl-2 col-md-4">
                <label for="to" class="form-label">Tanggal Akhir</label>
                <input type="date" id="to" name="to" value="{{ $filters['to'] }}" class="form-control">
            </div>

            <div class="col-xl-2 col-md-4">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    @foreach ($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>
                            {{ $statusOption === '' ? 'Semua Status' : ucfirst(str_replace('_', ' ', $statusOption)) }}
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

            <div class="col-xl-3 col-md-6">
                <label for="category_id" class="form-label">Kategori</label>
                <select id="category_id" name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 d-flex align-items-center gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="filter" style="width: 14px; height: 14px;"></i>
                    <span>Terapkan Filter</span>
                </button>
                <a href="{{ route('admin.analytics.products.index') }}"
                   class="btn btn-light rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Reset</span>
                </a>
                <a href="{{ route('admin.analytics.products.export', request()->query()) }}"
                   class="btn btn-success rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="download" style="width: 14px; height: 14px;"></i>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="btn btn-secondary rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="file-text" style="width: 14px; height: 14px;"></i>
                    <span>Admin Reports</span>
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="btn btn-info rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="package" style="width: 14px; height: 14px;"></i>
                    <span>Admin Products</span>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Produk dengan Aktivitas</p>
                <h4 class="mb-0 text-dark">{{ number_format($summary['total_products_with_activity'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Produk Dilihat</p>
                <h4 class="mb-0 text-dark">{{ number_format($summary['total_views'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Checkout Dimulai</p>
                <h4 class="mb-0 text-primary">{{ number_format($summary['total_checkout_started'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Payment Proof Uploaded</p>
                <h4 class="mb-0 text-info">{{ number_format($summary['total_payment_uploaded'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Order Paid</p>
                <h4 class="mb-0 text-success">{{ number_format($summary['total_paid_orders'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Paid / View Conversion</p>
                <h6 class="mb-0 text-dark">{{ number_format($summary['conversion_paid_views'], 2, ',', '.') }}%</h6>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-1">Top Funnel Produk</h5>
        <p class="text-muted mb-0 fs-13">Urutan berdasarkan jumlah view produk pada periode filter.</p>
    </div>
    <div class="card-body">
        @if ($topProducts->isNotEmpty())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>Produk</th>
                            <th style="width: 120px;">Views</th>
                            <th style="width: 140px;">Total Order</th>
                            <th style="width: 160px;">Order/View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProducts as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-medium text-dark">{{ $row['product']?->name ?? 'Produk tidak ditemukan' }}</td>
                                <td>{{ number_format($row['total_views'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['total_orders'], 0, ',', '.') }}</td>
                                <td class="fw-semibold">{{ number_format($row['conversion_order_views'], 2, ',', '.') }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Belum ada data penjualan pada periode ini.</p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-1">Performa Produk</h5>
        <p class="text-muted mb-0 fs-13">Funnel dari product view sampai order paid per produk.</p>
    </div>
    <div class="card-body">
        @if ($rows->isNotEmpty())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Views</th>
                            <th>Checkout Dimulai</th>
                            <th>Payment Uploaded</th>
                            <th>Order Paid</th>
                            <th>Total Order</th>
                            <th>Order/View</th>
                            <th>Paid/View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td class="fw-medium text-dark">{{ $row['product']?->name ?? 'Produk tidak ditemukan' }}</td>
                                <td>{{ $row['product']?->category?->name ?? '-' }}</td>
                                <td>{{ number_format($row['total_views'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['checkout_started'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['payment_uploaded_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['paid_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['total_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['conversion_order_views'], 2, ',', '.') }}%</td>
                                <td>{{ number_format($row['conversion_paid_views'], 2, ',', '.') }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i data-feather="inbox" class="text-muted mb-2" style="width: 44px; height: 44px;"></i>
                <h5 class="text-dark mb-1">Belum ada data penjualan pada periode ini.</h5>
                <p class="text-muted mb-0">Silakan ubah periode atau filter untuk melihat data lainnya.</p>
            </div>
        @endif
    </div>
</div>

@endsection
