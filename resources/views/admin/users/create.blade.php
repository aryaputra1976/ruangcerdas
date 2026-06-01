@extends('layouts.admin')

@php
    $title = 'Tambah User';
    $subtitle = 'Buat akun user baru.';
@endphp

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Form Tambah User</h5>
            <p class="text-muted mb-0 fs-13">Isi data akun yang akan dibuat.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">
            Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._form')
        </form>
    </div>
</div>

@endsection

