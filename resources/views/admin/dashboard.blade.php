@extends('layouts.admin')

@php
    $title = 'Dashboard';
    $subtitle = 'Ringkasan monitoring penjualan dan produk digital Ruang Cerdas.';

    $stat = fn (string $key, mixed $default = 0) => $stats[$key] ?? $default;

    $quickActions = [
        [
            'label' => 'Kelola Produk',
            'description' => 'Lihat dan update katalog produk digital.',
            'icon' => 'package',
            'color' => 'primary',
            'url' => route('admin.products.index'),
        ],
        [
            'label' => 'Order Menunggu Verifikasi',
            'description' => 'Fokus ke bukti pembayaran yang baru masuk.',
            'icon' => 'check-square',
            'color' => 'warning',
            'url' => route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PAYMENT_UPLOADED]),
        ],
        [
            'label' => 'Order Pending',
            'description' => 'Pantau order yang belum upload bukti bayar.',
            'icon' => 'clock',
            'color' => 'info',
            'url' => route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PENDING]),
        ],
        [
            'label' => 'Arsip Produk',
            'description' => 'Review produk yang sudah diarsipkan.',
            'icon' => 'archive',
            'color' => 'secondary',
            'url' => route('admin.products.archive'),
        ],
        [
            'label' => 'Tambah Produk Baru',
            'description' => 'Upload produk digital baru ke katalog.',
            'icon' => 'plus-circle',
            'color' => 'success',
            'url' => route('admin.products.create'),
        ],
    ];
@endphp

@section('content')

    <div class="row">
        <x-admin.stat-card
            title="Total Order Hari Ini"
            :value="$stat('today_orders')"
            description="Order baru sejak pukul 00.00"
            icon="shopping-cart"
            color="primary"
            :url="route('admin.orders.index')"
        />

        <x-admin.stat-card
            title="Order Menunggu Verifikasi"
            :value="$stat('waiting_verification')"
            description="Status payment_uploaded"
            icon="upload-cloud"
            color="warning"
            :url="route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PAYMENT_UPLOADED])"
        />

        <x-admin.stat-card
            title="Order Pending"
            :value="$stat('new_orders')"
            description="Belum upload bukti bayar"
            icon="clock"
            color="info"
            :url="route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PENDING])"
        />

        <x-admin.stat-card
            title="Order Paid Bulan Ini"
            :value="$stat('paid_orders_this_month')"
            description="Order paid sejak awal bulan"
            icon="check-circle"
            color="success"
            :url="route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PAID])"
        />

        <x-admin.stat-card
            title="Estimasi Omzet Bulan Ini"
            :value="\App\Support\Money::format($stat('month_revenue'))"
            description="Akumulasi dari paid orders"
            icon="dollar-sign"
            color="success"
        />

        <x-admin.stat-card
            title="Produk Aktif"
            :value="$stat('active_products')"
            description="Masih tampil di katalog"
            icon="package"
            color="primary"
            :url="route('admin.products.index')"
        />

        <x-admin.stat-card
            title="Produk Arsip"
            :value="$stat('archived_products')"
            description="Soft deleted / archived"
            icon="archive"
            color="secondary"
            :url="route('admin.products.archive')"
        />

        <x-admin.stat-card
            title="Order Expired"
            :value="$stat('expired_orders')"
            description="Sudah lewat masa pembayaran"
            icon="alert-circle"
            color="danger"
            :url="route('admin.orders.index', ['status' => \App\Models\Order::STATUS_EXPIRED])"
        />
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div>
                            <h5 class="card-title mb-1">
                                Ringkasan Penjualan
                            </h5>

                            <p class="text-muted mb-0 fs-13">
                                Fokus utama admin untuk monitoring order, omzet, dan antrian verifikasi.
                            </p>
                        </div>

                        <a href="{{ route('admin.orders.index') }}"
                           class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <i data-feather="list" style="width: 14px; height: 14px;"></i>
                            <span>Lihat Semua Order</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <p class="text-muted fs-13 mb-1">Omzet Hari Ini</p>
                                <h5 class="mb-1 text-dark">
                                    {{ \App\Support\Money::format($stat('today_revenue')) }}
                                </h5>
                                <div class="fs-13 text-muted">
                                    Dari order yang sudah paid hari ini.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <p class="text-muted fs-13 mb-1">Total Order</p>
                                <h5 class="mb-1 text-dark">
                                    {{ number_format((int) $stat('total_orders'), 0, ',', '.') }}
                                </h5>
                                <div class="fs-13 text-muted">
                                    Soft deleted order tetap terabaikan dari dashboard utama.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <p class="text-muted fs-13 mb-1">Order Rejected</p>
                                <h5 class="mb-1 text-dark">
                                    {{ number_format((int) $stat('rejected_orders'), 0, ',', '.') }}
                                </h5>
                                <div class="fs-13 text-muted">
                                    Tetap dipisahkan dari order yang masih bisa diproses.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <div class="rounded-3 bg-light p-3 h-100">
                                <p class="text-muted fs-13 mb-1">Produk Published</p>
                                <h4 class="mb-0">
                                    {{ number_format((int) $stat('published_products'), 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="rounded-3 bg-light p-3 h-100">
                                <p class="text-muted fs-13 mb-1">Kategori Aktif</p>
                                <h4 class="mb-0">
                                    {{ number_format((int) $stat('active_categories'), 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="rounded-3 bg-light p-3 h-100">
                                <p class="text-muted fs-13 mb-1">Log Download</p>
                                <h4 class="mb-0">
                                    {{ number_format((int) $stat('download_logs'), 0, ',', '.') }}
                                </h4>
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
                        Link Cepat
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($quickActions as $action)
                            <div class="col-12">
                                <a href="{{ $action['url'] }}" class="text-decoration-none">
                                    <div class="border rounded-3 p-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-2 bg-{{ $action['color'] }}-subtle text-{{ $action['color'] }} rounded-3">
                                                <i data-feather="{{ $action['icon'] }}" style="width: 18px; height: 18px;"></i>
                                            </div>

                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    {{ $action['label'] }}
                                                </div>

                                                <div class="text-muted fs-13">
                                                    {{ $action['description'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div>
                            <h5 class="card-title mb-1">
                                5 Order Terbaru
                            </h5>

                            <p class="text-muted mb-0 fs-13">
                                Snapshot order terakhir untuk pantauan harian admin.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (($latestOrders ?? collect())->isNotEmpty())
                        <div class="table-responsive table-card">
                            <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 150px;">Invoice</th>
                                        <th>Pembeli</th>
                                        <th>Produk</th>
                                        <th style="width: 130px;">Total</th>
                                        <th style="width: 145px;">Status</th>
                                        <th style="width: 120px;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($latestOrders as $order)
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

                                                <div class="text-muted fs-13 text-break" style="max-width: 190px;">
                                                    {{ $order->buyer_email }}
                                                </div>
                                            </td>

                                            <td>
                                                <span class="d-inline-block text-wrap" style="max-width: 180px;">
                                                    {{ $order->product->name ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="fw-semibold">
                                                {{ \App\Support\Money::format($order->price ?? 0) }}
                                            </td>

                                            <td>
                                                <x-admin.status-badge :status="$order->status" />
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
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <h5 class="card-title mb-0">
                            5 Order Menunggu Verifikasi
                        </h5>

                        @if (($waitingOrders ?? collect())->isNotEmpty())
                            <span class="badge bg-warning rounded-pill">
                                {{ $waitingOrders->count() }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if (($waitingOrders ?? collect())->isNotEmpty())
                        <ul class="list-group list-group-flush list-group-no-gutters">
                            @foreach ($waitingOrders as $order)
                                <li class="list-group-item px-0">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                                                 style="width: 38px; height: 38px;">
                                                <i data-feather="alert-circle" style="width: 18px; height: 18px;"></i>
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

                                                    <div class="fs-13 text-muted text-wrap" style="max-width: 190px;">
                                                        {{ $order->product->name ?? '-' }}
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <div class="fw-semibold text-dark fs-14">
                                                        {{ \App\Support\Money::format($order->price ?? 0) }}
                                                    </div>

                                                    <a href="{{ route('admin.orders.show', $order) }}"
                                                       class="fs-13 text-primary fw-semibold">
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
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div>
                            <h5 class="card-title mb-1">
                                5 Produk Terlaris
                            </h5>

                            <p class="text-muted mb-0 fs-13">
                                Dihitung dari paid orders, termasuk produk yang sudah diarsipkan bila masih punya penjualan lama.
                            </p>
                        </div>

                        <a href="{{ route('admin.products.index') }}"
                           class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <i data-feather="package" style="width: 14px; height: 14px;"></i>
                            <span>Kelola Produk</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (($topProducts ?? collect())->isNotEmpty())
                        <div class="table-responsive table-card">
                            <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th>Produk</th>
                                        <th style="width: 160px;">Kategori</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 120px;">Paid Order</th>
                                        <th style="width: 150px;">Estimasi Omzet</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($topProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark text-wrap" style="max-width: 280px;">
                                                    {{ $product->name }}
                                                </div>

                                                <div class="text-muted fs-13">
                                                    {{ $product->slug }}
                                                </div>
                                            </td>

                                            <td>
                                                @if ($product->category)
                                                    <span class="badge bg-info-subtle text-info rounded-pill">
                                                        {{ $product->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($product->trashed())
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                                        Arsip
                                                    </span>
                                                @elseif ($product->is_active)
                                                    <span class="badge bg-success-subtle text-success rounded-pill">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning rounded-pill">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="fw-semibold">
                                                {{ number_format((int) $product->paid_orders_count, 0, ',', '.') }}
                                            </td>

                                            <td class="fw-semibold">
                                                {{ \App\Support\Money::format((int) ($product->paid_orders_revenue ?? 0)) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-2">
                                <i data-feather="bar-chart-2" class="text-muted" style="width: 40px; height: 40px;"></i>
                            </div>

                            <h6 class="text-muted mb-0">
                                Belum ada data produk terlaris dari order paid.
                            </h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Ringkasan Sistem
                    </h5>
                </div>

                <div class="card-body">
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <span class="text-muted">Produk Tanpa File Private</span>
                            <span class="fw-semibold text-dark">
                                {{ number_format((int) $stat('missing_private_files_products'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <span class="text-muted">Total Download</span>
                            <span class="fw-semibold text-dark">
                                {{ number_format((int) $stat('downloads'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <span class="text-muted">Total Kategori</span>
                            <span class="fw-semibold text-dark">
                                {{ number_format((int) $stat('total_categories'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <div class="fw-semibold mb-1">
                            Fokus Harian
                        </div>

                        <div class="fs-13">
                            Prioritaskan order payment_uploaded, cek order pending yang menumpuk, dan pastikan produk aktif tetap punya file private yang valid.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
