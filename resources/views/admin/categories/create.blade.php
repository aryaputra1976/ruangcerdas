@extends('layouts.admin')

@php
    $title = 'Tambah Kategori';
    $subtitle = 'Tambahkan kategori baru untuk mengelompokkan produk digital.';
@endphp

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">
                    Form Tambah Kategori
                </h5>

                <p class="text-muted mb-0 fs-13">
                    Buat kategori agar katalog produk lebih mudah dicari dan dikelola.
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
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            @include('admin.categories._form')
        </form>
    </div>
</div>

@endsection