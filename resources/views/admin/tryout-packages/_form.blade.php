
@php
    $isEdit = isset($tryoutPackage);
    $package = $tryoutPackage ?? null;
    $isActive = old('is_active', $package?->is_active ?? true);
    $selectedType = old('tryout_type', $package?->tryout_type ?? \App\Support\TryoutBlueprint::TYPE_CPNS);
    $existingSectionCounts = collect($package?->sectionSummaries() ?? [])
        ->mapWithKeys(fn ($section) => [$section['key'] => $section['count']])
        ->all();
    $initialTotalQuestions = collect($sectionsByType[$selectedType] ?? [])
        ->sum(fn ($section) => (int) old('section_counts.' . $selectedType . '.' . $section['key'], $existingSectionCounts[$section['key']] ?? 0));
    $initialDurationMinutes = (int) old('duration_minutes', $package?->duration_minutes ?? 100);
    $initialMinutesPerQuestion = $initialTotalQuestions > 0
        ? round($initialDurationMinutes / $initialTotalQuestions, 2)
        : 0;
@endphp

<div class="row">
    <div class="col-xl-8">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Judul Paket <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title', $package?->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Tryout CPNS Intensif 2026" required autofocus>
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
                <input type="text" name="slug" value="{{ old('slug', $package?->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="Kosongkan untuk otomatis">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi singkat paket tryout">{{ old('description', $package?->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Harga <span class="text-danger">*</span></label>
                <input type="number" name="price" value="{{ old('price', $package?->price ?? 0) }}" class="form-control @error('price') is-invalid @enderror" min="0" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Durasi (menit) <span class="text-danger">*</span></label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $package?->duration_minutes ?? 100) }}" class="form-control @error('duration_minutes') is-invalid @enderror" min="1" required>
                @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Komposisi Soal <span class="text-danger">*</span></label>
                @error('section_counts')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <button type="button" class="btn btn-sm bg-success-subtle text-success rounded-pill px-3" id="fill-stock-counts" data-stock='@json($questionStockByType)'>
                        Isi Otomatis dari Stok
                    </button>
                    <button type="button" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3" id="reset-section-counts">
                        Reset Komposisi
                    </button>
                </div>
                <div class="row g-3">
                    @foreach ($sectionsByType as $type => $sections)
                        <div class="col-12">
                            <div class="border rounded-3 p-3 {{ $selectedType === $type ? 'border-primary' : '' }}">
                                <div class="fw-semibold text-dark mb-2">{{ $tryoutTypes[$type] }}</div>
                                <div class="row g-3">
                                    @foreach ($sections as $section)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $section['label'] }}</label>
                                            <input type="number" min="0" name="section_counts[{{ $type }}][{{ $section['key'] }}]" value="{{ old('section_counts.' . $type . '.' . $section['key'], $selectedType === $type ? ($existingSectionCounts[$section['key']] ?? 0) : 0) }}" class="form-control" data-section-count-input data-tryout-type="{{ $type }}" data-section-key="{{ $section['key'] }}">
                                            <div class="form-text">
                                                {{ $section['scoring_mode'] === 'weighted' ? 'Skor bertingkat.' : 'Jawaban tunggal.' }}
                                                Tersedia {{ $questionStockByType[$type][$section['key']] ?? 0 }} soal aktif.
                                            </div>
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
                <div class="alert alert-light border mt-3 mb-0">
                    Sistem akan menolak penyimpanan jika jumlah soal yang diminta melebihi stok soal aktif pada section terkait.
                </div>
            </div>
        </div>
        <div class="card border">
            <div class="card-header"><h5 class="card-title mb-0">Ringkasan Paket</h5></div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <div class="text-muted fs-13">Total Soal</div>
                        <div class="fs-4 fw-semibold text-dark" id="package-total-questions">{{ $initialTotalQuestions }}</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted fs-13">Estimasi Menit per Soal</div>
                        <div class="fs-4 fw-semibold text-dark" id="package-minutes-per-question">{{ number_format($initialMinutesPerQuestion, 2) }}</div>
                    </div>
                </div>
                <div class="alert alert-info mb-0" id="package-summary-note">
                    {{ $initialTotalQuestions > 0 ? 'Komposisi paket sudah terbaca. Sesuaikan durasi dan jumlah soal agar ritmenya nyaman untuk peserta.' : 'Isi komposisi soal untuk melihat total paket dan estimasi durasi per soal.' }}
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fillButton = document.getElementById('fill-stock-counts');
        const resetButton = document.getElementById('reset-section-counts');
        const typeSelect = document.querySelector('select[name="tryout_type"]');
        const durationInput = document.querySelector('input[name="duration_minutes"]');
        const inputs = Array.from(document.querySelectorAll('[data-section-count-input]'));
        const totalQuestionsEl = document.getElementById('package-total-questions');
        const minutesPerQuestionEl = document.getElementById('package-minutes-per-question');
        const summaryNoteEl = document.getElementById('package-summary-note');

        if (!fillButton || !resetButton || !typeSelect || !durationInput || inputs.length === 0 || !totalQuestionsEl || !minutesPerQuestionEl || !summaryNoteEl) {
            return;
        }

        const stockByType = JSON.parse(fillButton.dataset.stock || '{}');
        const updatePackageSummary = function () {
            const selectedType = typeSelect.value;
            const totalQuestions = inputs.reduce(function (sum, input) {
                if (input.dataset.tryoutType !== selectedType) {
                    return sum;
                }

                return sum + (parseInt(input.value || '0', 10) || 0);
            }, 0);
            const durationMinutes = parseInt(durationInput.value || '0', 10) || 0;
            const minutesPerQuestion = totalQuestions > 0 ? (durationMinutes / totalQuestions) : 0;

            totalQuestionsEl.textContent = String(totalQuestions);
            minutesPerQuestionEl.textContent = minutesPerQuestion.toFixed(2);

            if (totalQuestions < 1) {
                summaryNoteEl.textContent = 'Isi komposisi soal untuk melihat total paket dan estimasi durasi per soal.';
                return;
            }

            if (minutesPerQuestion < 1) {
                summaryNoteEl.textContent = 'Paket ini cukup padat. Pertimbangkan menambah durasi atau mengurangi jumlah soal.';
                return;
            }

            if (minutesPerQuestion > 3) {
                summaryNoteEl.textContent = 'Durasi per soal cukup longgar. Cocok untuk pembahasan lebih dalam atau soal kompleks.';
                return;
            }

            summaryNoteEl.textContent = 'Ritme paket terlihat seimbang untuk latihan tryout umum.';
        };

        fillButton.addEventListener('click', function () {
            const selectedType = typeSelect.value;

            inputs.forEach(function (input) {
                const type = input.dataset.tryoutType;
                const sectionKey = input.dataset.sectionKey;

                if (type !== selectedType) {
                    input.value = 0;
                    return;
                }

                input.value = stockByType[type]?.[sectionKey] ?? 0;
            });

            updatePackageSummary();
        });

        resetButton.addEventListener('click', function () {
            inputs.forEach(function (input) {
                input.value = 0;
            });

            updatePackageSummary();
        });

        typeSelect.addEventListener('change', updatePackageSummary);
        durationInput.addEventListener('input', updatePackageSummary);
        inputs.forEach(function (input) {
            input.addEventListener('input', updatePackageSummary);
        });

        updatePackageSummary();
    });
</script>
@endpush
