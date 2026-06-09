
@php
    $isEdit = isset($tryoutPackage);
    $isActive = old('is_active', $tryoutPackage->is_active ?? true);
    $selectedType = old('tryout_type', $tryoutPackage->tryout_type ?? \App\Support\TryoutBlueprint::TYPE_CPNS);
    $existingSectionCounts = collect($tryoutPackage->sectionSummaries() ?? [])->mapWithKeys(fn ($section) => [$section['key'] => $section['count']])->all();
@endphp

<div class="row">
    <div class="col-xl-8">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Judul Paket <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title', $tryoutPackage->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Tryout CPNS Intensif 2026" required autofocus>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Jenis Tryout <span class="text-danger">*</span></label>
                <select name="tryout_type" class="form-select @error('tryout_type') is-invalid @enderror" required>
                    @foreach ($tryoutTypes as $type => $label)
                        <option value="{{ $type }}" @selected($selectedType === $type)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tryout_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $tryoutPackage->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="Kosongkan untuk otomatis">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <div class="col-md-12">
                <label class="form-label">Komposisi Soal <span class="text-danger">*</span></label>
                @error('section_counts')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <div class="row g-3">
                    @foreach ($sectionsByType as $type => $sections)
                        <div class="col-12">
                            <div class="border rounded-3 p-3 {{ $selectedType === $type ? 'border-primary' : '' }}">
                                <div class="fw-semibold text-dark mb-2">{{ $tryoutTypes[$type] }}</div>
                                <div class="row g-3">
                                    @foreach ($sections as $section)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $section['label'] }}</label>
                                            <input type="number" min="0" name="section_counts[{{ $type }}][{{ $section['key'] }}]" value="{{ old('section_counts.' . $type . '.' . $section['key'], $selectedType === $type ? ($existingSectionCounts[$section['key']] ?? 0) : 0) }}" class="form-control">
                                            <div class="form-text">{{ $section['scoring_mode'] === 'weighted' ? 'Skor bertingkat.' : 'Jawaban tunggal.' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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
                    Paket aktif akan tampil di halaman kategori tryout sesuai jenisnya.
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
