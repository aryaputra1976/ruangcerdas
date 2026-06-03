@php
    $isEdit = isset($tryoutPackage);
    $isActive = old('is_active', $tryoutPackage->is_active ?? true);
@endphp

<div class="row">
    <div class="col-xl-8">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Judul Paket <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title', $tryoutPackage->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Tryout CPNS Intensif 2026" required autofocus>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Slug</label>
                <div class="input-group">
                    <span class="input-group-text">/tryout-cpns/</span>
                    <input type="text" name="slug" value="{{ old('slug', $tryoutPackage->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="Kosongkan untuk otomatis">
                </div>
                @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi singkat paket tryout">{{ old('description', $tryoutPackage->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Harga <span class="text-danger">*</span></label>
                <input type="number" name="price" value="{{ old('price', $tryoutPackage->price ?? 0) }}" class="form-control @error('price') is-invalid @enderror" min="0" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Durasi (menit) <span class="text-danger">*</span></label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $tryoutPackage->duration_minutes ?? 100) }}" class="form-control @error('duration_minutes') is-invalid @enderror" min="1" required>
                @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jumlah TWK <span class="text-danger">*</span></label>
                <input type="number" name="twk_count" value="{{ old('twk_count', $tryoutPackage->twk_count ?? 30) }}" class="form-control @error('twk_count') is-invalid @enderror" min="0" required>
                @error('twk_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jumlah TIU <span class="text-danger">*</span></label>
                <input type="number" name="tiu_count" value="{{ old('tiu_count', $tryoutPackage->tiu_count ?? 35) }}" class="form-control @error('tiu_count') is-invalid @enderror" min="0" required>
                @error('tiu_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jumlah TKP <span class="text-danger">*</span></label>
                <input type="number" name="tkp_count" value="{{ old('tkp_count', $tryoutPackage->tkp_count ?? 45) }}" class="form-control @error('tkp_count') is-invalid @enderror" min="0" required>
                @error('tkp_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border">
            <div class="card-header"><h5 class="card-title mb-0">Status</h5></div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($isActive)>
                    <label class="form-check-label fw-semibold" for="is_active">Paket Aktif</label>
                </div>
                <div class="alert alert-info mb-0">
                    Paket aktif akan tampil di halaman public Tryout CPNS.
                </div>
            </div>
        </div>
        <div class="card border">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Paket' }}</button>
                    <a href="{{ route('admin.tryout-packages.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill">Batal</a>
                </div>
            </div>
        </div>
    </div>
</div>
