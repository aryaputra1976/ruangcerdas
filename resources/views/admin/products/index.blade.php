@extends('layouts.admin')

@php
    $title = 'Produk Digital';
    $subtitle = 'Kelola produk digital, harga, status aktif, dan file ZIP private.';
@endphp

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Daftar Produk</h5>
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
            <div class="col-md-5">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Cari nama produk atau slug...">
            </div>

            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-3">
                    Filter
                </button>

                <a href="{{ route('admin.products.index') }}"
                   class="btn bg-secondary-subtle text-secondary rounded-pill px-3">
                    Reset
                </a>
            </div>
        </form>

        @if ($products->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Produk</th>
                            <th style="width: 160px;">Kategori</th>
                            <th style="width: 130px;">Harga Promo</th>
                            <th style="width: 130px;">Harga Normal</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 130px;">File ZIP</th>
                            <th style="width: 110px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $product->name }}
                                    </div>
                                    <div class="text-muted fs-13">
                                        {{ $product->slug }}
                                    </div>
                                </td>

                                <td>
                                    {{ $product->category->name ?? '-' }}
                                </td>

                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ \App\Support\Money::format($product->sale_price ?? $product->normal_price ?? 0) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ \App\Support\Money::format($product->normal_price ?? 0) }}
                                    </span>
                                </td>

                                <td>
                                    @if (($product->is_active ?? true) == true)
                                        <span class="badge bg-success-subtle text-success fw-semibold rounded-pill">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold rounded-pill">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($product->digital_file_path)
                                        <span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fw-semibold rounded-pill">
                                            Belum Ada
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

                <h5 class="text-dark mb-1">Belum ada produk</h5>
                <p class="text-muted mb-3">
                    Produk digital yang Anda jual akan muncul di sini.
                </p>

                <a href="{{ route('admin.products.create') }}"
                   class="btn btn-primary rounded-pill px-4">
                    Tambah Produk Pertama
                </a>
            </div>
        @endif

    </div>
</div>

@endsection