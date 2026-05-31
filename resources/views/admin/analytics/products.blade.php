@extends('layouts.admin')

@php
    $title = 'Analytics Produk';
    $subtitle = 'Analisis sederhana performa penjualan produk digital.';
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
                <p class="text-muted fs-13 mb-1">Produk Terjual</p>
                <h4 class="mb-0 text-dark">{{ number_format($summary['total_products_sold'], 0, ',', '.') }}</h4>
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
                <p class="text-muted fs-13 mb-1">Omzet Paid</p>
                <h4 class="mb-0 text-dark">{{ \App\Support\Money::format($summary['total_revenue_paid']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Rata-rata Order Paid</p>
                <h4 class="mb-0 text-primary">{{ \App\Support\Money::format($summary['average_paid_order_value']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Total Download</p>
                <h4 class="mb-0 text-info">{{ number_format($summary['total_downloads'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Produk Terlaris</p>
                <h6 class="mb-0 text-dark">{{ $summary['best_seller'] }}</h6>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-1">Produk Terlaris</h5>
        <p class="text-muted mb-0 fs-13">Urutan berdasarkan jumlah order paid lalu omzet paid.</p>
    </div>
    <div class="card-body">
        @if ($topProducts->isNotEmpty())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>Produk</th>
                            <th style="width: 160px;">Order Paid</th>
                            <th style="width: 180px;">Omzet Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProducts as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-medium text-dark">{{ $row['product']?->name ?? 'Produk tidak ditemukan' }}</td>
                                <td>{{ number_format($row['paid_orders'], 0, ',', '.') }}</td>
                                <td class="fw-semibold">{{ \App\Support\Money::format($row['paid_revenue']) }}</td>
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
        <p class="text-muted mb-0 fs-13">Ringkasan order per produk berdasarkan filter periode.</p>
    </div>
    <div class="card-body">
        @if ($rows->isNotEmpty())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Order Paid</th>
                            <th>Omzet Paid</th>
                            <th>Avg Paid Order</th>
                            <th>Download</th>
                            <th>Pending</th>
                            <th>Rejected</th>
                            <th>Total Order</th>
                            <th>Conversion</th>
                            <th style="min-width: 160px;">Progress Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td class="fw-medium text-dark">{{ $row['product']?->name ?? 'Produk tidak ditemukan' }}</td>
                                <td>{{ $row['product']?->category?->name ?? '-' }}</td>
                                <td>{{ number_format($row['paid_orders'], 0, ',', '.') }}</td>
                                <td>{{ \App\Support\Money::format($row['paid_revenue']) }}</td>
                                <td>{{ \App\Support\Money::format($row['average_order_value']) }}</td>
                                <td>{{ number_format($row['download_count'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['pending_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['rejected_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['total_orders'], 0, ',', '.') }}</td>
                                <td>{{ number_format($row['conversion_rate'], 2, ',', '.') }}%</td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $row['revenue_progress'] }}%"></div>
                                    </div>
                                    <div class="fs-13 text-muted mt-1">{{ number_format($row['revenue_progress'], 2, ',', '.') }}%</div>
                                </td>
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
