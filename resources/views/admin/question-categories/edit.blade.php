@extends('layouts.admin')

@php
    $title = 'Edit Kategori Soal';
    $subtitle = $questionCategory->name;
@endphp

@section('content')
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <h5 class="card-title mb-1">Form Edit Kategori Soal</h5>
                    <p class="text-muted mb-0 fs-13">Perbarui jenis tryout, section, nama, dan status kategori soal.</p>
                </div>
                <a href="{{ route('admin.question-categories.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">Kembali</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.question-categories.update', $questionCategory) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.question-categories._form', ['questionCategory' => $questionCategory])
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Ringkasan</h5></div>
            <div class="card-body">
                <h5 class="text-dark mb-1">{{ $questionCategory->name }}</h5>
                <p class="text-muted fs-13">{{ $questionCategory->slug }}</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $tryoutTypes[$questionCategory->tryout_type] ?? $questionCategory->tryout_type }}</span>
                    <span class="badge bg-info-subtle text-info rounded-pill">{{ $questionCategory->section_label }}</span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $questionCategory->questions_count ?? 0 }} soal</span>
                </div>
                <div class="border rounded-3 p-3">
                    <div class="text-muted fs-13 mb-1">Deskripsi</div>
                    <div class="text-dark">{{ $questionCategory->description ?: 'Belum ada deskripsi.' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
