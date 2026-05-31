@extends('layouts.admin')

@php
    $title = 'Dashboard';
    $subtitle = 'Ringkasan performa penjualan produk digital Ruang Cerdas.';

    $stat = fn (string $key, mixed $default = 0) => $stats[$key] ?? $default;

    $quickActions = [
        [
            'label' => 'Tambah Produk',
            'description' => 'Upload produk digital baru.',
            'icon' => 'plus-circle',
            'color' => 'primary',
            'url' => route('admin.products.create'),
        ],
        [
            'label' => 'Order Masuk',
            'description' => 'Lihat semua order pembeli.',
            'icon' => 'shopping-cart',
            'color' => 'success',
            'url' => route('admin.orders.index'),
        ],
        [
            'label' => 'Verifikasi Bayar',
            'description' => 'Cek bukti pembayaran.',
            'icon' => 'check-square',
            'color' => 'warning',
            'url' => route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PAYMENT_UPLOADED]),
        ],
        [
            'label' => 'Pengaturan Pembayaran',
            'description' => 'Atur rekening dan QRIS.',
            'icon' => 'credit-card',
            'color' => 'info',
            'url' => route('admin.payment-settings.edit'),
        ],
    ];
@endphp

@section('content')

    <div class="row">
        <x-admin.stat-card
            title="Total Produk"
            :value="$stat('total_products')"
            description="Semua produk"
            icon="package"
            color="primary"
            :url="route('admin.products.index')"
        />

        <x-admin.stat-card
            title="Produk Aktif"
            :value="$stat('active_products')"
            description="Siap dijual"
            icon="check-circle"
            color="success"
            :url="route('admin.products.index')"
        />

        <x-admin.stat-card
            title="Order Pending"
            :value="$stat('new_orders')"
            description="Belum upload bukti"
            icon="clock"
            color="warning"
            :url="route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PENDING])"
        />

        <x-admin.stat-card
            title="Menunggu Verifikasi"
            :value="$stat('waiting_verification')"
            description="Bukti bayar masuk"
            icon="upload-cloud"
            color="danger"
            :url="route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PAYMENT_UPLOADED])"
        />

        <x-admin.stat-card
            title="Omzet"
            :value="\App\Support\Money::format($stat('revenue'))"
            description="Total paid"
            icon="dollar-sign"
            color="success"
        />
    </div>

    <div class="row">
        <div class="col-xl-8">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div>
                            <h5 class="card-title mb-1">
                                Performa Penjualan
                            </h5>

                            <p class="text-muted mb-0 fs-13">
                                Ringkasan omzet, order paid, dan aktivitas download.
                            </p>
                        </div>

                        <a href="{{ route('admin.orders.index') }}"
                           class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <i data-feather="list" style="width: 14px; height: 14px;"></i>
                            <span>Lihat Order</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 bg-success-subtle text-success rounded-3">
                                        <i data-feather="trending-up" style="width: 20px; height: 20px;"></i>
                                    </div>

                                    <div>
                                        <p class="text-muted fs-13 mb-1">
                                            Omzet Hari Ini
                                        </p>

                                        <h5 class="mb-0 text-dark">
                                            {{ \App\Support\Money::format($stat('today_revenue')) }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                                        <i data-feather="calendar" style="width: 20px; height: 20px;"></i>
                                    </div>

                                    <div>
                                        <p class="text-muted fs-13 mb-1">
                                            Omzet Bulan Ini
                                        </p>

                                        <h5 class="mb-0 text-dark">
                                            {{ \App\Support\Money::format($stat('month_revenue')) }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 bg-info-subtle text-info rounded-3">
                                        <i data-feather="download" style="width: 20px; height: 20px;"></i>
                                    </div>

                                    <div>
                                        <p class="text-muted fs-13 mb-1">
                                            Total Download
                                        </p>

                                        <h5 class="mb-0 text-dark">
                                            {{ number_format((int) $stat('downloads'), 0, ',', '.') }}x
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row g-3 mt-1">

                        <div class="col-md-3 col-6">
                            <div class="rounded-3 bg-light p-3">
                                <p class="text-muted fs-13 mb-1">
                                    Total Order
                                </p>

                                <h4 class="mb-0">
                                    {{ number_format((int) $stat('total_orders'), 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="rounded-3 bg-light p-3">
                                <p class="text-muted fs-13 mb-1">
                                    Paid
                                </p>

                                <h4 class="mb-0 text-success">
                                    {{ number_format((int) $stat('paid_orders'), 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="rounded-3 bg-light p-3">
                                <p class="text-muted fs-13 mb-1">
                                    Rejected
                                </p>

                                <h4 class="mb-0 text-danger">
                                    {{ number_format((int) $stat('rejected_orders'), 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="rounded-3 bg-light p-3">
                                <p class="text-muted fs-13 mb-1">
                                    Log Download
                                </p>

                                <h4 class="mb-0 text-primary">
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
                        Quick Actions
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($quickActions as $action)
                            <div class="col-12">
                                <a href="{{ $action['url'] }}"
                                   class="text-decoration-none">
                                    <div class="border rounded-3 p-3 h-100 transition">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-2 bg-{{ $action['color'] }}-subtle text-{{ $action['color'] }} rounded-3">
                                                <i data-feather="{{ $action['icon'] }}" style="width: 19px; height: 19px;"></i>
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
                                Order Terbaru
                            </h5>

                            <p class="text-muted mb-0 fs-13">
                                Aktivitas order terbaru dari pembeli.
                            </p>
                        </div>

                        <a href="{{ route('admin.orders.index') }}"
                           class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <i data-feather="list" style="width: 14px; height: 14px;"></i>
                            <span>Lihat Semua</span>
                        </a>
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
                                        <th style="width: 125px;">Total</th>
                                        <th style="width: 145px;">Status</th>
                                        <th style="width: 130px;">Tanggal</th>
                                        <th style="width: 95px;" class="text-end">Aksi</th>
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
                                                <span class="d-inline-block text-wrap" style="max-width: 170px;">
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
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <h5 class="card-title mb-0">
                            Menunggu Verifikasi
                        </h5>

                        @if (($waitingOrders ?? collect())->count())
                            <span class="badge bg-danger rounded-pill">
                                {{ $waitingOrders->count() }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if (($waitingOrders ?? collect())->count())
                        <ul class="list-group list-group-flush list-group-no-gutters">
                            @foreach ($waitingOrders as $order)
                                <li class="list-group-item px-0">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                                                 style="width: 38px; height: 38px;">
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

                        <div class="mt-3">
                            <a href="{{ route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PAYMENT_UPLOADED]) }}"
                               class="btn btn-sm btn-warning w-100 rounded-pill">
                                Lihat Semua Verifikasi
                            </a>
                        </div>
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
                                Produk Terbaru
                            </h5>

                            <p class="text-muted mb-0 fs-13">
                                Produk digital terakhir yang ditambahkan.
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
                    @if (($latestProducts ?? collect())->count())
                        <div class="table-responsive table-card">
                            <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                <thead class="text-muted table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th style="width: 150px;">Kategori</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 140px;">Harga</th>
                                        <th style="width: 95px;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($latestProducts as $product)
                                        @php
                                            $isPublished = $product->published_at && $product->published_at->lte(now());
                                        @endphp

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
                                                <div class="d-flex flex-column gap-1 align-items-start">
                                                    @if ($product->is_active)
                                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                                            Nonaktif
                                                        </span>
                                                    @endif

                                                    @if ($isPublished)
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                                            Published
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning rounded-pill">
                                                            Draft
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>
                                                <span class="fw-semibold">
                                                    {{ \App\Support\Money::format($product->sale_price ?: $product->normal_price) }}
                                                </span>
                                            </td>

                                            <td class="text-end">
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                                    <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                                    <span>Edit</span>
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
                                <i data-feather="package" class="text-muted" style="width: 40px; height: 40px;"></i>
                            </div>

                            <h6 class="text-muted mb-3">
                                Belum ada produk.
                            </h6>

                            <a href="{{ route('admin.products.create') }}"
                               class="btn btn-primary rounded-pill px-4">
                                Tambah Produk
                            </a>
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
                            <span class="text-muted">Kategori Aktif</span>
                            <span class="fw-semibold text-dark">
                                {{ number_format((int) $stat('active_categories'), 0, ',', '.') }}
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

                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <span class="text-muted">Produk Published</span>
                            <span class="fw-semibold text-dark">
                                {{ number_format((int) $stat('published_products'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between gap-3">
                            <span class="text-muted">Max Download / Order</span>
                            <span class="fw-semibold text-dark">
                                {{ config('ruangcerdas.download.max_count', 5) }}
                            </span>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <div class="fw-semibold mb-1">
                            Tips
                        </div>

                        <div class="fs-13">
                            Fokus utama setiap hari: cek order menunggu verifikasi, pastikan QRIS/rekening aktif, dan pastikan produk punya file ZIP private.
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection