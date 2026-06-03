@extends('layouts.admin')

@php
    $title = 'Tambah Kategori Soal';
    $subtitle = 'Buat kategori soal baru untuk bank soal tryout.';
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Form Tambah Kategori Soal</h5>
            <p class="text-muted mb-0 fs-13">Pilih section dan isi nama kategori soal.</p>
        </div>
        <a href="{{ route('admin.question-categories.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.question-categories.store') }}">
            @csrf
            @include('admin.question-categories._form')
        </form>
    </div>
</div>
@endsection
