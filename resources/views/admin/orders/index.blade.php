@extends('layouts.admin')

@php
    $title = 'Order Masuk';
    $subtitle = 'Kelola order, bukti pembayaran, dan status pembelian produk digital.';
    $hasActiveFilter = filled($q ?? null) || filled($status ?? null) || filled($from ?? null) || filled($to ?? null);

    $statusCards = [
        [
            'key' => null,
            'label' => 'Semua Order',
            'count' => $counts['all'] ?? 0,
            'icon' => 'list',
            'class' => 'primary',
        ],
        [
            'key' => 'pending',
            'label' => 'Pending',
            'count' => $counts['pending'] ?? 0,
            'icon' => 'clock',
            'class' => 'warning',
        ],
        [
            'key' => 'payment_uploaded',
            'label' => 'Menunggu Verifikasi',
            'count' => $counts['payment_uploaded'] ?? 0,
            'icon' => 'upload-cloud',
            'class' => 'info',
        ],
        [
            'key' => 'paid',
            'label' => 'Paid',
            'count' => $counts['paid'] ?? 0,
            'icon' => 'check-circle',
            'class' => 'success',
        ],
        [
            'key' => 'rejected',
            'label' => 'Rejected',
            'count' => $counts['rejected'] ?? 0,
            'icon' => 'x-circle',
            'class' => 'danger',
        ],
        [
            'key' => 'expired',
            'label' => 'Expired',
            'count' => $counts['expired'] ?? 0,
            'icon' => 'slash',
            'class' => 'secondary',
        ],
    ];
@endphp

@section('content')

<div class="row g-3 mb-3">
    @foreach ($statusCards as $card)
        @php
            $isActive = blank($card['key'])
                ? blank($status)
                : $status === $card['key'];

            $queryParams = request()->except('status');
            if (!blank($card['key'])) {
                $queryParams['status'] = $card['key'];
            }
            $url = route('admin.orders.index', $queryParams);
        @endphp

        <div class="col-xl col-md-4 col-sm-6">
            <a href="{{ $url }}" class="text-decoration-none">
                <div class="card rc-dashboard-card h-100 {{ $isActive ? 'border border-' . $card['class'] : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 border border-{{ $card['class'] }} border-opacity-10 bg-{{ $card['class'] }}-subtle rounded-3">
                                <div class="bg-{{ $card['class'] }} rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 34px; height: 34px;">
                                    <i data-feather="{{ $card['icon'] }}"
                                       class="text-white"
                                       style="width: 17px; height: 17px;"></i>
                                </div>
                            </div>

                            <div>
                                <p class="text-muted fs-13 mb-1">
                                    {{ $card['label'] }}
                                </p>

                                <h4 class="mb-0 text-dark">
                                    {{ number_format($card['count'], 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Daftar Order</h5>
                <p class="text-muted mb-0 fs-13">
                    Semua transaksi pembelian produk digital Ruang Cerdas.
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if ($hasActiveFilter)
                    <a href="{{ route('admin.orders.index') }}"
                       class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                        <span>Reset Filter</span>
                    </a>
                @endif

                <a href="{{ route('admin.dashboard') }}"
                   class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                    <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 mb-3">
            <div class="col-lg-4 col-md-6">
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       class="form-control"
                       placeholder="Cari invoice / nama / email / WhatsApp">
            </div>
            <div class="col-lg-2 col-md-6">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="payment_uploaded" @selected($status === 'payment_uploaded')>Menunggu Verifikasi</option>
                    <option value="paid" @selected($status === 'paid')>Paid</option>
                    <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                    <option value="expired" @selected($status === 'expired')>Expired</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-lg-2 col-md-6">
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-lg-2 col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light w-100">Reset</a>
            </div>
        </form>

        @if ($hasActiveFilter)
            <div class="mb-3 p-2 rounded bg-light border text-muted fs-13">
                Filter aktif:
                @if (filled($q))
                    <span class="badge bg-primary-subtle text-primary rounded-pill">Q: {{ $q }}</span>
                @endif
                @if (filled($status))
                    <span class="badge bg-info-subtle text-info rounded-pill">Status: {{ $status }}</span>
                @endif
                @if (filled($from))
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Dari: {{ $from }}</span>
                @endif
                @if (filled($to))
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Sampai: {{ $to }}</span>
                @endif
            </div>
        @endif

        @if (($orders ?? collect())->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th style="width: 155px;">Invoice</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th style="width: 130px;">Total</th>
                            <th style="width: 165px;">Status</th>
                            <th style="width: 145px;">Tanggal</th>
                            <th style="width: 210px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark text-break" style="max-width: 145px;">
                                        {{ $order->invoice_number }}
                                    </div>

                                    @if ($order->coupon_code)
                                        <div class="fs-13 mt-1">
                                            <span class="badge bg-success-subtle text-success rounded-pill">
                                                Kupon: {{ $order->coupon_code }}
                                            </span>
                                        </div>
                                    @endif

                                    @if ($order->payment_proof_path)
                                        <div class="fs-13 mt-1">
                                            <span class="badge bg-info-subtle text-info rounded-pill">
                                                Bukti terupload
                                            </span>
                                        </div>
                                    @endif

                                    @if ($order->notes->count() > 0)
                                        <div class="fs-13 mt-1 d-flex flex-wrap gap-1">
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                                Ada catatan
                                            </span>

                                            @if ($order->notes->contains(fn ($note) => $note->is_pinned))
                                                <span class="badge bg-warning-subtle text-warning rounded-pill">
                                                    Pinned
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-medium text-dark">
                                        {{ $order->buyer_name }}
                                    </div>

                                    <div class="text-muted fs-13 text-break">
                                        {{ $order->buyer_email }}
                                    </div>

                                    @if ($order->buyer_whatsapp)
                                        <div class="text-muted fs-13">
                                            WA: {{ $order->buyer_whatsapp }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-medium text-dark text-wrap" style="max-width: 220px;">
                                        {{ $order->product->name ?? '-' }}
                                    </div>

                                    @if ($order->product?->category)
                                        <div class="text-muted fs-13">
                                            {{ $order->product->category->name }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ \App\Support\Money::format($order->price ?? 0) }}
                                    </span>
                                </td>

                                <td>
                                    <x-admin.status-badge :status="$order->status" />

                                    @if ($order->status === 'paid')
                                        <div class="text-muted fs-13 mt-1">
                                            Download: {{ $order->download_count ?? 0 }}x
                                        </div>
                                    @endif
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
                                    <div class="d-inline-flex align-items-center justify-content-end gap-2 flex-wrap">
                                        @if (in_array($order->status, ['expired', 'rejected'], true))
                                            <form method="POST"
                                                  action="{{ route('admin.orders.destroy', $order) }}"
                                                  onsubmit="return confirm('Order ini akan disembunyikan dari daftar, bukan dihapus permanen. Lanjutkan?')"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3 d-inline-flex align-items-center gap-1 rc-action-btn">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1 rc-action-btn">
                                            <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                            <span>Detail</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (method_exists($orders, 'links'))
                <div class="mt-3">
                    {{ $orders->links('vendor.pagination.ruangcerdas') }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i data-feather="inbox" class="text-muted" style="width: 46px; height: 46px;"></i>
                </div>

                <h5 class="text-dark mb-1">Belum ada order</h5>

                <p class="text-muted mb-0">
                    @if ($hasActiveFilter)
                        Tidak ada order dengan filter yang dipilih.
                    @else
                        Order pembelian akan muncul di halaman ini.
                    @endif
                </p>

                @if ($hasActiveFilter)
                    <div class="mt-3">
                        <a href="{{ route('admin.orders.index') }}"
                           class="btn btn-sm btn-primary rounded-pill px-3">
                            Tampilkan Semua Order
                        </a>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

@endsection
