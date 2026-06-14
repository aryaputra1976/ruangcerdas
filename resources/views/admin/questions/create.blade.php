@extends('layouts.admin')

@php
    $title = 'Tambah Soal';
    $subtitle = 'Tambahkan soal baru ke bank soal tryout.';
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Form Tambah Soal</h5>
            <p class="text-muted mb-0 fs-13">Isi section, teks soal, dan opsi jawaban sesuai aturan tiap section.</p>
        </div>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.questions.store') }}">
            @csrf
            @include('admin.questions._form')
        </form>
    </div>
</div>
@endsection

