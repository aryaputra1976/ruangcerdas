@extends('layouts.admin')

@php
    $title = 'Edit Kategori';
    $subtitle = $category->name;
@endphp

@section('content')

<div class="row">

    <div class="col-xl-8">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h5 class="card-title mb-1">
                            Form Edit Kategori
                        </h5>

                        <p class="text-muted mb-0 fs-13">
                            Perbarui nama, slug, deskripsi, urutan, dan status kategori.
                        </p>
                    </div>

                    <a href="{{ route('admin.categories.index') }}"
                       class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                    @csrf
                    @method('PUT')

                    @include('admin.categories._form', ['category' => $category])
                </form>
            </div>
        </div>

    </div>

    <div class="col-xl-4">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Ringkasan Kategori
                </h5>
            </div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="mx-auto rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                         style="width: 120px; height: 120px;">
                        <i data-feather="folder" style="width: 42px; height: 42px;"></i>
                    </div>
                </div>

                <h5 class="text-dark text-center mb-1">
                    {{ $category->name }}
                </h5>

                <p class="text-muted text-center fs-13 mb-4">
                    {{ $category->slug }}
                </p>

                <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
                    @if ($category->is_active)
                        <span class="badge bg-success-subtle text-success fw-semibold rounded-pill">
                            Aktif
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary fw-semibold rounded-pill">
                            Nonaktif
                        </span>
                    @endif

                    <span class="badge bg-info-subtle text-info fw-semibold rounded-pill">
                        {{ number_format((int) ($category->products_count ?? 0), 0, ',', '.') }} Produk
                    </span>
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="text-muted fs-13 mb-1">
                        Jumlah Produk
                    </div>

                    <div class="fw-semibold text-dark">
                        {{ number_format((int) ($category->products_count ?? 0), 0, ',', '.') }} produk
                    </div>
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="text-muted fs-13 mb-1">
                        Urutan
                    </div>

                    <div class="fw-semibold text-dark">
                        {{ number_format((int) ($category->sort_order ?? 0), 0, ',', '.') }}
                    </div>
                </div>

                <div class="border rounded-3 p-3">
                    <div class="text-muted fs-13 mb-1">
                        Deskripsi
                    </div>

                    <div class="text-dark">
                        {{ $category->description ?: 'Belum ada deskripsi.' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger-subtle">
                <h5 class="card-title text-danger mb-0">
                    Zona Bahaya
                </h5>
            </div>

            <div class="card-body">
                @if (($category->products_count ?? 0) > 0)
                    <div class="alert alert-warning">
                        Kategori ini masih memiliki produk, sehingga tidak bisa dihapus.
                    </div>
                @else
                    <p class="text-muted">
                        Hapus kategori jika benar-benar tidak digunakan. Data kategori akan dihapus dari sistem.
                    </p>

                    <form method="POST"
                          action="{{ route('admin.categories.destroy', $category) }}"
                          onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-danger rounded-pill px-4 d-inline-flex align-items-center gap-1">
                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                            <span>Hapus Kategori</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection