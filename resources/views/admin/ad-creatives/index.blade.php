@extends('layouts.admin')

@php
    $title = 'Generator Iklan';
    $subtitle = 'Kelola creative iklan PNG 9:16 untuk produk dan kampanye Ruang Cerdas.';
    $actions = new \Illuminate\Support\HtmlString(
        '<div class="d-flex gap-2">'
        . '<a href="' . route('admin.ad-creatives.create', ['mode' => 'bulk']) . '" class="btn btn-light border rounded-pill px-4 d-inline-flex align-items-center gap-1">'
        . '<i data-feather="layers" style="width: 14px; height: 14px;"></i><span>Generate Massal</span></a>'
        . '<a href="' . route('admin.ad-creatives.create') . '" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-1">'
        . '<i data-feather="plus" style="width: 14px; height: 14px;"></i><span>Buat Iklan</span></a>'
        . '</div>'
    );
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Daftar Creative Iklan</h5>
                <p class="text-muted mb-0 fs-13">Semua hasil generator iklan 9:16 yang sudah dibuat admin.</p>
            </div>
            <form method="GET" action="{{ route('admin.ad-creatives.index') }}" class="d-flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul atau produk...">
                <button type="submit" class="btn btn-light border rounded-pill px-3">Cari</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        @if ($creatives->count())
            <div class="row g-3">
                @foreach ($creatives as $creative)
                    <div class="col-xl-3 col-md-6">
                        <div class="card border h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="rounded-3 overflow-hidden border bg-light mb-3 text-center">
                                    <img src="{{ asset('storage/' . $creative->image_path) }}"
                                         alt="{{ $creative->title }}"
                                         class="img-fluid"
                                         style="width: 100%; aspect-ratio: 9/16; object-fit: cover;">
                                </div>
                                <h6 class="mb-1 text-dark">{{ $creative->title }}</h6>
                                <div class="text-muted fs-13 mb-2">{{ $creative->product?->name ?? 'Tanpa Produk' }}</div>
                                <div class="text-muted fs-13 mb-3">{{ $creative->created_at?->format('d M Y H:i') }}</div>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="{{ route('admin.ad-creatives.show', $creative) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Detail</a>
                                    <a href="{{ route('admin.ad-creatives.edit', $creative) }}" class="btn btn-sm bg-warning-subtle text-warning rounded-pill px-3">Edit</a>
                                    <a href="{{ route('admin.ad-creatives.download', $creative) }}" class="btn btn-sm bg-success-subtle text-success rounded-pill px-3">Download</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">
                {{ $creatives->links('vendor.pagination.ruangcerdas') }}
            </div>
        @else
            <div class="text-center py-5">
                <i data-feather="image" class="text-muted mb-2" style="width: 42px; height: 42px;"></i>
                <h5 class="text-dark mb-1">Belum ada creative iklan</h5>
                <p class="text-muted mb-3">Mulai dari satu template viral note untuk produk unggulan Anda.</p>
                <a href="{{ route('admin.ad-creatives.create') }}" class="btn btn-primary rounded-pill px-4">Buat Iklan Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
