@extends('layouts.admin')

@php
    $title = 'Tambah Produk';
    $subtitle = 'Tambahkan produk digital baru untuk dijual di Ruang Cerdas.';
@endphp

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">
                    Form Tambah Produk
                </h5>

                <p class="text-muted mb-0 fs-13">
                    Lengkapi informasi produk, harga, status publikasi, cover, dan file digital private.
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
              action="{{ route('admin.products.store') }}"
              enctype="multipart/form-data">
            @csrf

            @include('admin.products._form')
        </form>
    </div>
</div>

@endsection
