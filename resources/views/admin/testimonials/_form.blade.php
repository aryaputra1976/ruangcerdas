@php
    $isEdit = isset($testimonial);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $testimonial->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Role</label>
        <input type="text" name="role" value="{{ old('role', $testimonial->role ?? '') }}"
               class="form-control @error('role') is-invalid @enderror">
        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Testimonial <span class="text-danger">*</span></label>
        <textarea name="content" rows="5" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $testimonial->content ?? '') }}</textarea>
        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
        <input type="number" min="1" max="5" name="rating" value="{{ old('rating', $testimonial->rating ?? 5) }}"
               class="form-control @error('rating') is-invalid @enderror" required>
        @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}"
               class="form-control @error('sort_order') is-invalid @enderror">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="w-100">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $testimonial->is_featured ?? false))>
                <label class="form-check-label" for="is_featured">Featured</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $testimonial->is_active ?? true))>
                <label class="form-check-label" for="is_active">Aktif</label>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary rounded-pill px-4">
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Testimonial' }}
    </button>
    <a href="{{ route('admin.testimonials.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill px-4">Batal</a>
</div>
