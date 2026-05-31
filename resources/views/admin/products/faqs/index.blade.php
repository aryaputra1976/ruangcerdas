@extends('layouts.admin')

@php
    $title = 'FAQ Produk';
    $subtitle = $product->name;
@endphp

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">FAQ Produk</h5>
                <p class="text-muted mb-0 fs-13">Kelola pertanyaan yang tampil di halaman detail produk public.</p>
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
        <h5 class="card-title mb-0">Tambah FAQ</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.faqs.store', $product) }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Pertanyaan</label>
                    <input type="text" name="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question') }}" maxlength="255">
                    @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Jawaban</label>
                    <textarea name="answer" rows="4" class="form-control @error('answer') is-invalid @enderror" maxlength="2000">{{ old('answer') }}</textarea>
                    @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Urutan</label>
                    <input type="number" min="0" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_new_faq" @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active_new_faq">Aktif</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan FAQ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar FAQ</h5>
    </div>
    <div class="card-body">
        @if ($faqs->count())
            <div class="d-flex flex-column gap-3">
                @foreach ($faqs as $faq)
                    <div class="border rounded-3 p-3">
                        <form method="POST" action="{{ route('admin.products.faqs.update', [$product, $faq]) }}">
                            @csrf
                            @method('PATCH')
                            <div class="row g-2">
                                <div class="col-12">
                                    <input type="text" name="question" class="form-control" value="{{ $faq->question }}" maxlength="255">
                                </div>
                                <div class="col-12">
                                    <textarea name="answer" rows="3" class="form-control" maxlength="2000">{{ $faq->answer }}</textarea>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ $faq->sort_order }}">
                                </div>
                                <div class="col-md-3 d-flex align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="faq_active_{{ $faq->id }}" @checked($faq->is_active)>
                                        <label class="form-check-label" for="faq_active_{{ $faq->id }}">Aktif</label>
                                    </div>
                                </div>
                                <div class="col-md-7 d-flex justify-content-md-end gap-2 flex-wrap">
                                    @if ($faq->is_active)
                                        <span class="badge bg-success-subtle text-success rounded-pill align-self-center">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill align-self-center">Nonaktif</span>
                                    @endif
                                    <button type="submit" class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">Update</button>
                                </div>
                            </div>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.products.faqs.destroy', [$product, $faq]) }}"
                              class="mt-2"
                              onsubmit="return confirm('Hapus FAQ ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">Belum ada FAQ untuk produk ini.</p>
        @endif
    </div>
</div>
@endsection
