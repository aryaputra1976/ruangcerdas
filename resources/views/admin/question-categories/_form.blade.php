@php
    $isEdit = isset($questionCategory);
    $isActive = old('is_active', $questionCategory->is_active ?? true);
    $selectedType = old('tryout_type', $questionCategory->tryout_type ?? \App\Support\TryoutBlueprint::TYPE_CPNS);
    $selectedPosition = old('position_target', $questionCategory->position_target ?? '');
@endphp

<div class="row">
    <div class="col-xl-8">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $questionCategory->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: TWK Nasionalisme" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $questionCategory->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="Kosongkan untuk otomatis">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jenis Tryout <span class="text-danger">*</span></label>
                <select name="tryout_type" class="form-select @error('tryout_type') is-invalid @enderror" required>
                    @foreach ($tryoutTypes as $type => $label)
                        <option value="{{ $type }}" @selected($selectedType === $type)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tryout_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Section <span class="text-danger">*</span></label>
                <select name="section" class="form-select @error('section') is-invalid @enderror" required>
                    <option value="">Pilih section</option>
                    @foreach ($sectionsByType as $type => $sections)
                        <optgroup label="{{ $tryoutTypes[$type] }}">
                            @foreach ($sections as $sectionKey => $sectionLabel)
                                <option value="{{ $sectionKey }}" @selected(old('section', $questionCategory->section ?? '') === $sectionKey)>{{ $sectionLabel }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4" id="position-target-wrapper">
                <label class="form-label">Target Jabatan</label>
                <select name="position_target" class="form-select @error('position_target') is-invalid @enderror">
                    <option value="">Umum / Tidak dibatasi</option>
                    @foreach ($positionsByType as $type => $positions)
                        @foreach ($positions as $positionKey => $positionLabel)
                            <option value="{{ $positionKey }}" data-tryout-type="{{ $type }}" @selected($selectedPosition === $positionKey)>{{ $positionLabel }}</option>
                        @endforeach
                    @endforeach
                </select>
                <div class="form-text">Khusus PPPK Tendik, pilih jabatan agar kategori tidak tercampur.</div>
                @error('position_target')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi kategori soal">{{ old('description', $questionCategory->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border">
            <div class="card-header"><h5 class="card-title mb-0">Status</h5></div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($isActive)>
                    <label class="form-check-label fw-semibold" for="is_active">Kategori Aktif</label>
                </div>
                <div class="alert alert-info mb-0">Kategori aktif dapat dipilih saat input bank soal.</div>
            </div>
        </div>
        <div class="card border">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}</button>
                    <a href="{{ route('admin.question-categories.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill">Batal</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.querySelector('select[name="tryout_type"]');
        const positionWrapper = document.getElementById('position-target-wrapper');
        const positionSelect = document.querySelector('select[name="position_target"]');

        if (!typeSelect || !positionWrapper || !positionSelect) {
            return;
        }

        const updatePositionVisibility = function () {
            const selectedType = typeSelect.value;
            const options = Array.from(positionSelect.options);
            const hasScopedPositions = options.some(function (option) {
                return option.dataset.tryoutType === selectedType;
            });

            positionWrapper.classList.toggle('d-none', !hasScopedPositions);

            options.forEach(function (option) {
                if (!option.dataset.tryoutType) {
                    option.hidden = hasScopedPositions;
                    return;
                }

                option.hidden = option.dataset.tryoutType !== selectedType;
            });

            if (!hasScopedPositions) {
                positionSelect.value = '';
            } else if (positionSelect.selectedOptions[0]?.hidden) {
                const firstVisible = options.find(function (option) {
                    return !option.hidden && option.dataset.tryoutType === selectedType;
                });

                positionSelect.value = firstVisible ? firstVisible.value : '';
            }
        };

        typeSelect.addEventListener('change', updatePositionVisibility);
        updatePositionVisibility();
    });
</script>
@endpush
