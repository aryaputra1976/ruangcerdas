@extends('layouts.admin')

@php
    $title = 'Testimoni Produk';
    $subtitle = $product->name;
@endphp

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Testimoni Produk</h5>
                <p class="text-muted mb-0 fs-13">Kelola review pembeli yang tampil di halaman detail produk dan dipakai untuk structured data.</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">
                    Edit Produk
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">
                    Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-light border mb-0">
            <strong>Produk:</strong> {{ $product->name }} <span class="text-muted">({{ $product->slug }})</span>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Tambah Testimoni</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.reviews.store', $product) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Pembeli</label>
                    <input type="text" name="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name') }}" maxlength="255">
                    @error('author_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Judul</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="255">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating</label>
                    <input type="number" min="1" max="5" name="rating" class="form-control @error('rating') is-invalid @enderror" value="{{ old('rating', 5) }}">
                    @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Review</label>
                    <input type="date" name="reviewed_at" class="form-control @error('reviewed_at') is-invalid @enderror" value="{{ old('reviewed_at') }}">
                    @error('reviewed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_visible" value="1" class="form-check-input" id="is_visible_new_review" @checked(old('is_visible', true))>
                        <label class="form-check-label" for="is_visible_new_review">Tampilkan di public</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Isi Testimoni</label>
                    <textarea name="body" rows="4" class="form-control @error('body') is-invalid @enderror" maxlength="5000">{{ old('body') }}</textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Testimoni</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Testimoni</h5>
    </div>
    <div class="card-body">
        @if ($reviews->count())
            <div class="d-flex flex-column gap-3">
                @foreach ($reviews as $review)
                    <div class="border rounded-3 p-3">
                        <form method="POST" action="{{ route('admin.products.reviews.update', [$product, $review]) }}">
                            @csrf
                            @method('PATCH')
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="author_name" class="form-control" value="{{ $review->author_name }}" maxlength="255">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="title" class="form-control" value="{{ $review->title }}" maxlength="255" placeholder="Judul review">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" min="1" max="5" name="rating" class="form-control" value="{{ $review->rating }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="reviewed_at" class="form-control" value="{{ $review->reviewed_at?->format('Y-m-d') }}">
                                </div>
                                <div class="col-12">
                                    <textarea name="body" rows="3" class="form-control" maxlength="5000">{{ $review->body }}</textarea>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_visible" value="1" class="form-check-input" id="review_visible_{{ $review->id }}" @checked($review->is_visible)>
                                        <label class="form-check-label" for="review_visible_{{ $review->id }}">Tampilkan di public</label>
                                    </div>
                                </div>
                                <div class="col-md-8 d-flex justify-content-md-end gap-2 flex-wrap">
                                    @if ($review->is_visible)
                                        <span class="badge bg-success-subtle text-success rounded-pill align-self-center">Visible</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill align-self-center">Hidden</span>
                                    @endif
                                    <span class="badge bg-warning-subtle text-warning rounded-pill align-self-center">Rating {{ $review->rating }}/5</span>
                                    <button type="submit" class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">Update</button>
                                </div>
                            </div>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.products.reviews.destroy', [$product, $review]) }}"
                              class="mt-2"
                              onsubmit="return confirm('Hapus testimoni ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">Belum ada testimoni untuk produk ini.</p>
        @endif
    </div>
</div>
@endsection
