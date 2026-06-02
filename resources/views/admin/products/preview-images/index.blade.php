@extends('layouts.admin')

@php
    $title = 'Preview Images Produk';
    $subtitle = $product->name;
@endphp

@section('content')
<div class="row g-3 mb-3">
    <div class="col-12">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Edit Produk</span>
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Preview Image</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.preview-images.store', $product) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Gambar Preview <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" maxlength="255">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Caption</label>
                        <textarea name="caption" rows="4" class="form-control @error('caption') is-invalid @enderror" maxlength="2000">{{ old('caption') }}</textarea>
                        @error('caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="form-control @error('sort_order') is-invalid @enderror">
                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4">Upload Preview</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Daftar Preview Image</h5>
            </div>
            <div class="card-body">
                @if ($previewImages->isEmpty())
                    <div class="text-muted">Belum ada preview image untuk produk ini.</div>
                @else
                    <div class="row g-3">
                        @foreach ($previewImages as $previewImage)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <img src="{{ asset('storage/' . $previewImage->image_path) }}" alt="{{ $previewImage->title ?: $product->name }}" class="img-fluid rounded-3 border" style="width: 100%; height: 180px; object-fit: cover;">
                                    <div class="mt-2">
                                        <div class="fw-semibold text-dark">{{ $previewImage->title ?: 'Tanpa judul' }}</div>
                                        @if ($previewImage->caption)
                                            <div class="text-muted fs-13 mt-1">{{ $previewImage->caption }}</div>
                                        @endif
                                        <div class="text-muted fs-13 mt-1">Sort: {{ $previewImage->sort_order }}</div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.products.preview-images.destroy', [$product, $previewImage]) }}" class="mt-3" onsubmit="return confirm('Hapus preview image ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
