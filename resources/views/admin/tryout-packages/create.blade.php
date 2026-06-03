@extends('layouts.admin')

@php
    $title = 'Tambah Paket Tryout';
    $subtitle = 'Buat paket tryout CPNS baru.';
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Form Tambah Paket</h5>
            <p class="text-muted mb-0 fs-13">Atur judul, harga, durasi, dan komposisi soal paket.</p>
        </div>
        <a href="{{ route('admin.tryout-packages.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tryout-packages.store') }}">
            @csrf
            @include('admin.tryout-packages._form')
        </form>
    </div>
</div>
@endsection
