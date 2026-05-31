@extends('layouts.admin')

@php
    $title = 'Produk Digital';
    $subtitle = 'Kelola produk digital, harga, status aktif, publikasi, dan file ZIP private.';

    $totalProducts = method_exists($products, 'total') ? $products->total() : $products->count();

    $hasFilter = request()->filled('q') || request()->filled('category_id') || request()->filled('file_status');
@endphp

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">
                    Daftar Produk
                </h5>

                <p class="text-muted mb-0 fs-13">
                    Semua produk digital yang dijual di Ruang Cerdas.
                </p>
            </div>

            <a href="{{ route('admin.products.create') }}"
               class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 mb-4">
            <div class="col-lg-5 col-md-6">
                <div class="position-relative">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Cari nama produk atau slug...">
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <select name="file_status" class="form-select">
                    <option value="">Semua File</option>
                    <option value="missing" @selected(request('file_status') === 'missing')>File belum ada</option>
                    <option value="ready" @selected(request('file_status') === 'ready')>File siap</option>
                </select>
            </div>

            <div class="col-lg-1 col-md-6 d-flex gap-2 flex-wrap">
                <button type="submit"
                        class="btn btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                    <i data-feather="filter" style="width: 14px; height: 14px;"></i>
                    <span>Filter</span>
                </button>

                @if ($hasFilter)
                    <a href="{{ route('admin.products.index') }}"
                       class="btn bg-danger-subtle text-danger rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @else
                    <a href="{{ route('admin.products.index') }}"
                       class="btn bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i>
                        <span>Refresh</span>
                    </a>
                @endif
            </div>
        </form>

        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
            <div class="text-muted fs-13">
                Total data:
                <span class="fw-semibold text-dark">
                    {{ number_format($totalProducts, 0, ',', '.') }}
                </span>
                produk
            </div>

            @if ($hasFilter)
                <div class="text-muted fs-13">
                    Filter aktif:
                    @if (request('q'))
                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                            Keyword: {{ request('q') }}
                        </span>
                    @endif

                    @if (request('category_id'))
                        @php
                            $selectedCategory = $categories->firstWhere('id', (int) request('category_id'));
                        @endphp

                        <span class="badge bg-info-subtle text-info rounded-pill">
                            Kategori: {{ $selectedCategory->name ?? request('category_id') }}
                        </span>
                    @endif

                    @if (request('file_status'))
                        <span class="badge bg-warning-subtle text-warning rounded-pill">
                            File: {{ request('file_status') === 'missing' ? 'Belum ada' : 'Siap' }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        @if ($products->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Produk</th>
                            <th style="width: 150px;">Kategori</th>
                            <th style="width: 155px;">Harga</th>
                            <th style="width: 170px;">Pembeli Pertama</th>
                            <th style="width: 150px;">Status</th>
                            <th style="width: 130px;">File ZIP</th>
                            <th style="width: 110px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $product)
                            @php
                                $isPublished = $product->published_at && $product->published_at->lte(now());
                                $hasDiscount = !empty($product->sale_price) && $product->sale_price < $product->normal_price;
                                $hasFirstBuyerPrice = !empty($product->first_buyer_price) && $product->first_buyer_price > 0;
                            @endphp

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if ($product->cover_image)
                                                <img src="{{ asset('storage/' . $product->cover_image) }}"
                                                     alt="{{ $product->name }}"
                                                     class="rounded-3 border"
                                                     style="width: 52px; height: 52px; object-fit: cover;">
                                            @else
                                                <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                                     style="width: 52px; height: 52px;">
                                                    <i data-feather="package" style="width: 22px; height: 22px;"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <div class="fw-semibold text-dark text-wrap" style="max-width: 260px;">
                                                {{ $product->name }}
                                            </div>

                                            <div class="text-muted fs-13 text-break" style="max-width: 260px;">
                                                {{ $product->slug }}
                                            </div>

                                            <div class="d-flex gap-1 flex-wrap mt-1">
                                                @if ($product->is_featured)
                                                    <span class="badge bg-warning-subtle text-warning rounded-pill">
                                                        Featured
                                                    </span>
                                                @endif

                                                @if ($isPublished)
                                                    <span class="badge bg-success-subtle text-success rounded-pill">
                                                        Published
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                                        Draft
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if ($product->category)
                                        <span class="badge bg-info-subtle text-info fw-semibold rounded-pill">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($hasDiscount)
                                        <div class="fw-semibold text-success">
                                            {{ \App\Support\Money::format($product->sale_price ?? 0) }}
                                        </div>

                                        <div class="text-muted fs-13 text-decoration-line-through">
                                            {{ \App\Support\Money::format($product->normal_price ?? 0) }}
                                        </div>
                                    @else
                                        <div class="fw-semibold text-dark">
                                            {{ \App\Support\Money::format($product->normal_price ?? 0) }}
                                        </div>

                                        <div class="text-muted fs-13">
                                            Harga normal
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if ($hasFirstBuyerPrice)
                                        <div class="fw-semibold text-dark">
                                            {{ \App\Support\Money::format($product->first_buyer_price ?? 0) }}
                                        </div>

                                        <div class="text-muted fs-13">
                                            Kuota:
                                            {{ number_format((int) ($product->first_buyer_quota ?? 0), 0, ',', '.') }}
                                        </div>
                                    @else
                                        <span class="text-muted">Tidak aktif</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex flex-column gap-1 align-items-start">
                                        @if (($product->is_active ?? true) == true)
                                            <span class="badge bg-success-subtle text-success fw-semibold rounded-pill">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary fw-semibold rounded-pill">
                                                Nonaktif
                                            </span>
                                        @endif

                                        @if ($isPublished)
                                            <span class="text-muted fs-13">
                                                {{ $product->published_at->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-13">
                                                Belum publish
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if ($product->isMissingPrivateFile())
                                        <span class="badge bg-danger-subtle text-danger fw-semibold rounded-pill">
                                            File belum ada
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success fw-semibold rounded-pill">
                                            File siap
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1 rc-action-btn">
                                        <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                        <span>Edit</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i data-feather="package" class="text-muted" style="width: 46px; height: 46px;"></i>
                </div>

                <h5 class="text-dark mb-1">
                    @if ($hasFilter)
                        Produk tidak ditemukan
                    @else
                        Belum ada produk
                    @endif
                </h5>

                <p class="text-muted mb-3">
                    @if ($hasFilter)
                        Tidak ada produk yang sesuai dengan filter pencarian.
                    @else
                        Produk digital yang Anda jual akan muncul di sini.
                    @endif
                </p>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    @if ($hasFilter)
                        <a href="{{ route('admin.products.index') }}"
                           class="btn bg-secondary-subtle text-secondary rounded-pill px-4">
                            Reset Filter
                        </a>
                    @endif

                    <a href="{{ route('admin.products.create') }}"
                       class="btn btn-primary rounded-pill px-4">
                        Tambah Produk
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
