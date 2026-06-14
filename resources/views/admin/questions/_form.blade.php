@php
    $isEdit = isset($question);
    $questionOptions = $question->options ?? collect();
    $isActive = old('is_active', $question->is_active ?? true);
    $selectedType = old('tryout_type', $question->tryout_type ?? \App\Support\TryoutBlueprint::TYPE_CPNS);
    $selectedSection = old('section', $question->section ?? 'twk');
    $selectedPosition = old('position_target', $question->position_target ?? '');
    $selectedCategoryId = (string) old('question_category_id', $question->question_category_id ?? '');
    $optionLabelMap = collect(array_keys($tryoutTypes))
        ->mapWithKeys(function ($type) use ($sectionsByType) {
            return [
                $type => collect($sectionsByType[$type] ?? [])
                    ->mapWithKeys(fn ($sectionLabel, $sectionKey) => [
                        $sectionKey => \App\Support\TryoutBlueprint::optionLabels($type, $sectionKey),
                    ])
                    ->all(),
            ];
        })
        ->all();
    $categoryMap = collect($categories)
        ->map(fn ($category) => [
            'id' => (string) $category->id,
            'name' => ($tryoutTypes[$category->tryout_type] ?? $category->tryout_type) . ' - ' . $category->section_label . ' - ' . $category->name . ($category->position_target_label ? ' (' . $category->position_target_label . ')' : ''),
            'tryout_type' => $category->tryout_type,
            'section' => strtolower((string) $category->section),
            'position_target' => $category->position_target ?? '',
        ])
        ->values()
        ->all();
    $defaultOptions = [];

    foreach ($optionLabels as $label) {
        $existingOption = $questionOptions->firstWhere('option_label', $label) ?? null;
        $defaultOptions[$label] = [
            'option_text' => old("options.$label.option_text", $existingOption->option_text ?? ''),
            'score' => old("options.$label.score", $existingOption->score ?? ($label === 'A' ? 5 : 0)),
            'is_correct' => old("options.$label.is_correct", $existingOption->is_correct ?? false),
        ];
    }
@endphp

<div class="row">
    <div class="col-xl-8">
        <div class="row g-3">
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
                    @foreach ($sectionsByType as $type => $sections)
                        <optgroup label="{{ $tryoutTypes[$type] }}">
                            @foreach ($sections as $sectionKey => $sectionLabel)
                                <option value="{{ $sectionKey }}" @selected($selectedSection === $sectionKey)>{{ $sectionLabel }}</option>
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
                <div class="form-text">Pilih jabatan agar bank soal PPPK Tendik tidak tercampur.</div>
                @error('position_target')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Kategori Soal</label>
                <select name="question_category_id" class="form-select @error('question_category_id') is-invalid @enderror" id="question-category-select">
                    <option value="">Tanpa kategori</option>
                </select>
                @error('question_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Teks Soal <span class="text-danger">*</span></label>
                <textarea name="question_text" rows="7" class="form-control @error('question_text') is-invalid @enderror" placeholder="Tulis soal di sini..." required>{{ old('question_text', $question->question_text ?? '') }}</textarea>
                @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Pembahasan</label>
                <textarea name="explanation" rows="5" class="form-control @error('explanation') is-invalid @enderror" placeholder="Opsional, isi pembahasan soal">{{ old('explanation', $question->explanation ?? '') }}</textarea>
                @error('explanation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Level <span class="text-danger">*</span></label>
                <select name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required>
                    @foreach (['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('difficulty', $question->difficulty ?? 'medium') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="card border mt-4">
            <div class="card-header">
                <h5 class="card-title mb-1">Opsi Jawaban</h5>
                <p class="text-muted fs-13 mb-0" id="option-rule-hint">Isi opsi sesuai aturan skor per section.</p>
            </div>
            <div class="card-body">
                @error('options')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="row g-3">
                    @foreach ($optionLabels as $label)
                        <div class="col-12" data-option-row data-option-label="{{ $label }}">
                            <div class="border rounded-3 p-3">
                                <div class="row g-3 align-items-start">
                                    <div class="col-md-1">
                                        <label class="form-label">Label</label>
                                        <input type="text" class="form-control" value="{{ $label }}" readonly>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Teks Opsi {{ $label }} <span class="text-danger">*</span></label>
                                        <textarea name="options[{{ $label }}][option_text]" rows="3" class="form-control @error("options.$label.option_text") is-invalid @enderror" placeholder="Isi opsi {{ $label }}">{{ $defaultOptions[$label]['option_text'] }}</textarea>
                                        @error("options.$label.option_text")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Benar</label>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" name="options[{{ $label }}][is_correct]" value="1" class="form-check-input" id="is_correct_{{ $label }}" @checked($defaultOptions[$label]['is_correct'])>
                                            <label class="form-check-label" for="is_correct_{{ $label }}">Ya</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Skor</label>
                                        <input type="number" name="options[{{ $label }}][score]" value="{{ $defaultOptions[$label]['score'] }}" min="0" max="5" class="form-control @error("options.$label.score") is-invalid @enderror">
                                        @error("options.$label.score")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
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
                    <label class="form-check-label fw-semibold" for="is_active">Soal Aktif</label>
                </div>
                <div class="alert alert-info mb-0">
                    <div class="fw-semibold mb-1">Aturan cepat</div>
                    <div class="fs-13">CPNS TWK/TIU: benar 5, salah 0.</div>
                    <div class="fs-13">CPNS TKP: A-E dengan skor 5, 4, 3, 2, 1.</div>
                    <div class="fs-13">PPPK Tendik Teknis: benar 5, salah 0.</div>
                    <div class="fs-13">PPPK Tendik Manajerial, Sosial Kultural, Wawancara: A-D dengan skor 4, 3, 2, 1.</div>
                </div>
            </div>
        </div>
        <div class="card border">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Soal' }}</button>
                    <a href="{{ route('admin.questions.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill">Batal</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.querySelector('select[name="tryout_type"]');
        const sectionSelect = document.querySelector('select[name="section"]');
        const positionSelect = document.querySelector('select[name="position_target"]');
        const positionWrapper = document.getElementById('position-target-wrapper');
        const categorySelect = document.getElementById('question-category-select');
        const optionRows = Array.from(document.querySelectorAll('[data-option-row]'));
        const optionRuleHint = document.getElementById('option-rule-hint');
        const optionLabelMap = @json($optionLabelMap);
        const categoryMap = @json($categoryMap);
        const selectedCategoryId = @json($selectedCategoryId);

        if (!typeSelect || !sectionSelect || !positionSelect || !positionWrapper || optionRows.length === 0) {
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

        const updateOptionVisibility = function () {
            const selectedType = typeSelect.value;
            const selectedSection = sectionSelect.value;
            const activeLabels = optionLabelMap?.[selectedType]?.[selectedSection] ?? ['A', 'B', 'C', 'D', 'E'];
            const usesFourOptions = activeLabels.length === 4;

            optionRows.forEach(function (row) {
                const optionLabel = row.dataset.optionLabel;
                const shouldShow = activeLabels.includes(optionLabel);
                const fields = row.querySelectorAll('textarea, input[type="checkbox"], input[type="number"]');

                row.classList.toggle('d-none', !shouldShow);

                fields.forEach(function (field) {
                    field.disabled = !shouldShow;
                });
            });

            if (optionRuleHint) {
                optionRuleHint.textContent = usesFourOptions
                    ? 'Section ini memakai 4 opsi jawaban: A-D.'
                    : 'Section ini memakai 5 opsi jawaban: A-E.';
            }
        };

        const updateCategoryOptions = function () {
            if (!categorySelect) {
                return;
            }

            const selectedType = typeSelect.value;
            const selectedSection = sectionSelect.value;
            const selectedPosition = positionSelect.value || '';
            const previousValue = categorySelect.value || selectedCategoryId || '';
            const filteredCategories = categoryMap.filter(function (category) {
                return category.tryout_type === selectedType
                    && category.section === selectedSection
                    && (category.position_target || '') === selectedPosition;
            });

            categorySelect.innerHTML = '<option value="">Tanpa kategori</option>';

            filteredCategories.forEach(function (category) {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;

                if (category.id === previousValue) {
                    option.selected = true;
                }

                categorySelect.appendChild(option);
            });
        };

        const syncFormState = function () {
            updatePositionVisibility();
            updateOptionVisibility();
            updateCategoryOptions();
        };

        typeSelect.addEventListener('change', syncFormState);
        sectionSelect.addEventListener('change', syncFormState);
        positionSelect.addEventListener('change', updateCategoryOptions);

        syncFormState();
    });
</script>
@endpush
