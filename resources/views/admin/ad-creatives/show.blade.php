@extends('layouts.admin')

@php
    $title = 'Detail Iklan';
    $subtitle = 'Lihat hasil generator iklan, edit ulang kontennya, dan unduh dalam beberapa format untuk campaign.';
    $actions = new \Illuminate\Support\HtmlString(
        '<div class="d-flex gap-2">'
        . '<a href="' . route('admin.ad-creatives.edit', $creative) . '" class="btn btn-light border rounded-pill px-4 d-inline-flex align-items-center gap-1">'
        . '<i data-feather="edit-3" style="width: 14px; height: 14px;"></i><span>Edit</span></a>'
        . '<form method="POST" action="' . route('admin.ad-creatives.duplicate', $creative) . '">'
        . csrf_field()
        . '<button type="submit" class="btn btn-light border rounded-pill px-4 d-inline-flex align-items-center gap-1">'
        . '<i data-feather="copy" style="width: 14px; height: 14px;"></i><span>Duplikat</span></button></form>'
        . '<a href="' . route('admin.ad-creatives.download', $creative) . '" class="btn btn-success rounded-pill px-4 d-inline-flex align-items-center gap-1">'
        . '<i data-feather="download" style="width: 14px; height: 14px;"></i><span>Download PNG</span></a>'
        . '<a href="' . route('admin.ad-creatives.index') . '" class="btn btn-light border rounded-pill px-4">Kembali</a>'
        . '</div>'
    );
@endphp

@section('content')
<div class="row g-3">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">Preview Creative</h5>
                <p class="text-muted mb-0 fs-13">Format {{ strtoupper($creative->format) }} {{ $creative->width }}x{{ $creative->height }}.</p>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $creative->image_path) }}"
                     alt="{{ $creative->title }}"
                     class="img-fluid rounded-3 border"
                     style="max-height: 720px; width: auto;">
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">Detail Konten Iklan</h5>
                <p class="text-muted mb-0 fs-13">Semua teks yang dipakai untuk generate creative ini.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Produk</div>
                            <div class="fw-semibold text-dark">{{ $creative->product?->name ?? 'Tanpa Produk' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Template & Ukuran</div>
                            <div class="fw-semibold text-dark">{{ $creative->template_key }}</div>
                            <div class="text-muted fs-13 mt-2">{{ $creative->width }} x {{ $creative->height }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Title</div>
                            <div class="fw-semibold text-dark">{{ $creative->title }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">CTA</div>
                            <div class="fw-semibold text-dark">{{ $creative->cta_text }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded-3 p-3">
                            <div class="text-muted fs-13 mb-1">Headline</div>
                            <div class="fw-semibold text-dark">{{ $creative->headline }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded-3 p-3">
                            <div class="text-muted fs-13 mb-1">Body</div>
                            <div class="text-dark">{{ $creative->body }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-2">Bullet Manfaat</div>
                            @if (!empty($creative->bullets))
                                <ul class="mb-0 ps-3">
                                    @foreach ($creative->bullets as $bullet)
                                        <li class="mb-1">{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-muted">Tidak ada bullet.</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted fs-13 mb-1">Brand</div>
                            <div class="fw-semibold text-dark">{{ $creative->brand_text }}</div>
                            <div class="text-muted fs-13 mt-3">Dibuat Oleh</div>
                            <div class="fw-semibold text-dark">{{ $creative->creator?->name ?? 'System' }}</div>
                            <div class="text-muted fs-13 mt-3">Waktu Buat</div>
                            <div class="text-dark">{{ $creative->created_at?->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-muted fs-13 mb-2">Download Format</div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.ad-creatives.download', $creative) }}" class="btn btn-sm bg-success-subtle text-success rounded-pill px-3">PNG</a>
                        <a href="{{ route('admin.ad-creatives.download', ['adCreative' => $creative, 'format' => 'jpg']) }}" class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">JPG</a>
                        <a href="{{ route('admin.ad-creatives.download', ['adCreative' => $creative, 'format' => 'webp']) }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">WEBP</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.ad-creatives.destroy', $creative) }}" class="mt-4" onsubmit="return confirm('Hapus creative iklan ini? File PNG juga akan dihapus dari storage publik.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Hapus Creative</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
