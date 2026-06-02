@php($isEdit = isset($leadMagnet))
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Judul <span class="text-danger">*</span></label>
        <input type="text" name="title" value="{{ old('title', $leadMagnet->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $leadMagnet->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="otomatis jika kosong">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $leadMagnet->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Cover Image</label>
        <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">File Gratis</label>
        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if (!empty($leadMagnet?->file_path))<div class="form-text">File saat ini tersedia.</div>@endif
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $leadMagnet->is_active ?? false))>
            <label class="form-check-label" for="is_active">Aktif (tampil di public)</label>
        </div>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Lead Magnet' }}</button>
    <a href="{{ route('admin.lead-magnets.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill px-4">Batal</a>
</div>
