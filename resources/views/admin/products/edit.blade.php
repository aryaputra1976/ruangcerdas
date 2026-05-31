@extends('layouts.admin')

@php
    $title = 'Edit Produk';
    $subtitle = $product->name;

    $isPublished = $product->published_at && $product->published_at->lte(now());
    $hasDiscount = !empty($product->sale_price) && $product->sale_price < $product->normal_price;
@endphp

@section('content')

<div class="row">

    <div class="col-xl-8">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h5 class="card-title mb-1">
                            Form Edit Produk
                        </h5>

                        <p class="text-muted mb-0 fs-13">
                            Perbarui informasi produk, harga, status publikasi, cover, dan file ZIP private.
                        </p>
                    </div>

                    <a href="{{ route('admin.products.index') }}"
                       class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form method="POST"
                      action="{{ route('admin.products.update', $product) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('admin.products._form', ['product' => $product])
                </form>
            </div>
        </div>

    </div>

    <div class="col-xl-4">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Ringkasan Produk
                </h5>
            </div>

            <div class="card-body">

                <div class="text-center mb-4">
                    @if ($product->cover_image)
                        <img src="{{ asset('storage/' . $product->cover_image) }}"
                             alt="{{ $product->name }}"
                             class="rounded-4 border"
                             style="width: 160px; height: 160px; object-fit: cover;">
                    @else
                        <div class="mx-auto rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                             style="width: 160px; height: 160px;">
                            <i data-feather="package" style="width: 48px; height: 48px;"></i>
                        </div>
                    @endif
                </div>

                <h5 class="text-dark mb-1 text-center">
                    {{ $product->name }}
                </h5>

                <p class="text-muted text-center fs-13 mb-4">
                    {{ $product->slug }}
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                    @if ($product->is_active)
                        <span class="badge bg-success-subtle text-success fw-semibold rounded-pill">
                            Aktif
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary fw-semibold rounded-pill">
                            Nonaktif
                        </span>
                    @endif

                    @if ($isPublished)
                        <span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill">
                            Published
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning fw-semibold rounded-pill">
                            Draft
                        </span>
                    @endif

                    @if ($product->is_featured)
                        <span class="badge bg-warning-subtle text-warning fw-semibold rounded-pill">
                            Featured
                        </span>
                    @endif

                    @if ($product->digital_file_path)
                        <span class="badge bg-info-subtle text-info fw-semibold rounded-pill">
                            File ZIP Ada
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger fw-semibold rounded-pill">
                            File ZIP Belum Ada
                        </span>
                    @endif
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="text-muted fs-13 mb-1">
                        Harga Normal
                    </div>

                    <div class="fw-semibold text-dark">
                        {{ \App\Support\Money::format($product->normal_price ?? 0) }}
                    </div>
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="text-muted fs-13 mb-1">
                        Harga Promo
                    </div>

                    @if ($product->sale_price)
                        <div class="fw-semibold {{ $hasDiscount ? 'text-success' : 'text-dark' }}">
                            {{ \App\Support\Money::format($product->sale_price ?? 0) }}
                        </div>

                        @if ($hasDiscount)
                            <div class="text-muted fs-13">
                                Lebih murah dari harga normal.
                            </div>
                        @endif
                    @else
                        <div class="text-muted">
                            Tidak aktif
                        </div>
                    @endif
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="text-muted fs-13 mb-1">
                        Harga Pembeli Pertama
                    </div>

                    @if ($product->first_buyer_price)
                        <div class="fw-semibold text-dark">
                            {{ \App\Support\Money::format($product->first_buyer_price ?? 0) }}
                        </div>

                        <div class="text-muted fs-13">
                            Kuota:
                            {{ number_format((int) ($product->first_buyer_quota ?? 0), 0, ',', '.') }}
                        </div>
                    @else
                        <div class="text-muted">
                            Tidak aktif
                        </div>
                    @endif
                </div>

                <div class="border rounded-3 p-3">
                    <div class="text-muted fs-13 mb-1">
                        File Digital
                    </div>

                    @if ($product->digital_file_path)
                        <div class="fw-semibold text-dark text-break">
                            {{ $product->download_filename ?: basename($product->digital_file_path) }}
                        </div>

                        <div class="text-muted fs-13 text-break mt-1">
                            {{ $product->digital_file_path }}
                        </div>
                    @else
                        <div class="text-danger">
                            File ZIP belum tersedia.
                        </div>
                    @endif
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
                <p class="text-muted">
                    Nonaktifkan produk agar tidak tampil dan tidak dijual di website. Data produk tidak dihapus permanen.
                </p>

                <form method="POST"
                      action="{{ route('admin.products.destroy', $product) }}"
                      onsubmit="return confirm('Yakin ingin menonaktifkan produk ini? Produk tidak akan tampil di website.');">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger rounded-pill px-4 d-inline-flex align-items-center gap-1"
                            @disabled(! $product->is_active && blank($product->published_at))>
                        <i data-feather="slash" style="width: 14px; height: 14px;"></i>
                        <span>Nonaktifkan Produk</span>
                    </button>
                </form>

                @if (! $product->is_active && blank($product->published_at))
                    <div class="alert alert-warning mt-3 mb-0">
                        Produk ini sudah nonaktif dan tidak dipublikasikan.
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection