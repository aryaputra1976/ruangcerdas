@extends('layouts.admin')

@php
    $title = 'Edit Paket Tryout';
    $subtitle = $tryoutPackage->title;
@endphp

@section('content')
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <h5 class="card-title mb-1">Form Edit Paket</h5>
                    <p class="text-muted mb-0 fs-13">Perbarui detail paket tryout beserta struktur section-nya.</p>
                </div>
                <a href="{{ route('admin.tryout-packages.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">Kembali</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tryout-packages.update', $tryoutPackage) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.tryout-packages._form', ['tryoutPackage' => $tryoutPackage])
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Ringkasan Paket</h5></div>
            <div class="card-body">
                <h5 class="text-dark mb-1">{{ $tryoutPackage->title }}</h5>
                <p class="text-muted fs-13">{{ $tryoutPackage->slug }}</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge {{ $tryoutPackage->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill">
                        {{ $tryoutPackage->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $tryoutPackage->tryout_type_label }}</span>
                    <span class="badge bg-info-subtle text-info rounded-pill">{{ $tryoutPackage->sessions_count ?? 0 }} sesi</span>
                </div>
                <div class="border rounded-3 p-3 mb-3">
                    <div class="text-muted fs-13 mb-1">Harga</div>
                    <div class="fw-semibold text-dark">{{ $tryoutPackage->price > 0 ? 'Rp ' . number_format($tryoutPackage->price, 0, ',', '.') : 'Gratis' }}</div>
                </div>
                <div class="border rounded-3 p-3">
                    <div class="text-muted fs-13 mb-1">Komposisi</div>
                    <div class="fw-semibold text-dark">{{ collect($tryoutPackage->sectionSummaries())->map(fn ($section) => $section['label'] . ' ' . $section['count'])->implode(' · ') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
