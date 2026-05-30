@extends('layouts.admin')

@php
    $title = 'Kategori Produk';
    $subtitle = 'Kelola kategori untuk mengelompokkan produk digital.';
@endphp

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Daftar Kategori</h5>
                <p class="text-muted mb-0 fs-13">
                    Kategori membantu pembeli menemukan produk dengan lebih mudah.
                </p>
            </div>

            <a href="{{ route('admin.categories.create') }}"
               class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Cari nama kategori atau slug...">
            </div>

            <div class="col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-3">
                    Filter
                </button>

                <a href="{{ route('admin.categories.index') }}"
                   class="btn bg-secondary-subtle text-secondary rounded-pill px-3">
                    Reset
                </a>
            </div>
        </form>

        @if ($categories->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th style="width: 150px;">Jumlah Produk</th>
                            <th style="width: 130px;">Dibuat</th>
                            <th style="width: 110px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $category->name }}
                                    </div>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $category->slug }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill">
                                        {{ $category->products_count }} produk
                                    </span>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $category->created_at?->format('d M Y') }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
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
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i data-feather="folder" class="text-muted" style="width: 46px; height: 46px;"></i>
                </div>

                <h5 class="text-dark mb-1">Belum ada kategori</h5>
                <p class="text-muted mb-3">
                    Tambahkan kategori untuk produk digital Anda.
                </p>

                <a href="{{ route('admin.categories.create') }}"
                   class="btn btn-primary rounded-pill px-4">
                    Tambah Kategori Pertama
                </a>
            </div>
        @endif

    </div>
</div>

@endsection