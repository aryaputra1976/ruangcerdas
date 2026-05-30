@extends('layouts.admin')

@php
    $title = 'Edit Produk';
    $subtitle = $product->name;
@endphp

@section('content')

<form method="POST"
      action="{{ route('admin.products.update', $product) }}"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.products._form', ['product' => $product])
</form>

<div class="card mt-4 border-danger">
    <div class="card-header bg-danger-subtle">
        <h5 class="card-title text-danger mb-0">
            Zona Bahaya
        </h5>
    </div>

    <div class="card-body">
        <p class="text-muted">
            Nonaktifkan produk agar tidak tampil dan tidak dijual di website.
        </p>

        <form method="POST"
              action="{{ route('admin.products.destroy', $product) }}"
              onsubmit="return confirm('Yakin ingin menonaktifkan produk ini?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger rounded-pill px-4">
                Nonaktifkan Produk
            </button>
        </form>
    </div>
</div>

@endsection