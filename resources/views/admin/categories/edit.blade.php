@extends('layouts.admin')

@php
    $title = 'Edit Kategori';
    $subtitle = $category->name;
@endphp

@section('content')

<form method="POST" action="{{ route('admin.categories.update', $category) }}">
    @csrf
    @method('PUT')

    @include('admin.categories._form', ['category' => $category])
</form>

<div class="card mt-4 border-danger">
    <div class="card-header bg-danger-subtle">
        <h5 class="card-title text-danger mb-0">
            Zona Bahaya
        </h5>
    </div>

    <div class="card-body">
        <p class="text-muted mb-3">
            Kategori hanya bisa dihapus jika belum memiliki produk.
        </p>

        <form method="POST"
              action="{{ route('admin.categories.destroy', $category) }}"
              onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger rounded-pill px-4">
                Hapus Kategori
            </button>
        </form>
    </div>
</div>

@endsection