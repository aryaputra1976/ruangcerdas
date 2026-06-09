@php
    $isEdit = isset($question);
    $questionOptions = $question->options ?? collect();
    $isActive = old('is_active', $question->is_active ?? true);
    $selectedType = old('tryout_type', $question->tryout_type ?? \App\Support\TryoutBlueprint::TYPE_CPNS);
    $defaultOptions = [];

    foreach ($optionLabels as $label) {
        $existingOption = $questionOptions->firstWhere('option_label', $label) ?? null;
        $defaultOptions[$label] = [
            'option_text' => old("options.$label.option_text", $existingOption->option_text ?? ''),
            'score' => old("options.$label.score", $existingOption->score ?? ($label === 'A' ? 5 : 0)),
            'is_correct' => old("options.$label.is_correct", $existingOption->is_correct ?? false),
        ];
    }

    $currentSection = old('section', $question->section ?? 'twk');
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
                                <option value="{{ $sectionKey }}" @selected($currentSection === $sectionKey)>{{ $sectionLabel }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori Soal</label>
                <select name="question_category_id" class="form-select @error('question_category_id') is-invalid @enderror">
                    <option value="">Tanpa kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('question_category_id', $question->question_category_id ?? '') === (string) $category->id)>
                            {{ $tryoutTypes[$category->tryout_type] ?? $category->tryout_type }} - {{ $category->section_label }} - {{ $category->name }}
                        </option>
                    @endforeach
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
                <h5 class="card-title mb-1">Opsi Jawaban A-E</h5>
                <p class="text-muted fs-13 mb-0">Section objective wajib satu jawaban benar. Section weighted gunakan skor 1-5 untuk setiap opsi.</p>
            </div>
            <div class="card-body">
                @error('options')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="row g-3">
                    @foreach ($optionLabels as $label)
                        <div class="col-12">
                            <div class="border rounded-3 p-3">
                                <div class="row g-3 align-items-start">
                                    <div class="col-md-1">
                                        <label class="form-label">Label</label>
                                        <input type="text" class="form-control" value="{{ $label }}" readonly>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Teks Opsi {{ $label }} <span class="text-danger">*</span></label>
                                        <textarea name="options[{{ $label }}][option_text]" rows="3" class="form-control @error("options.$label.option_text") is-invalid @enderror" placeholder="Isi opsi {{ $label }}" required>{{ $defaultOptions[$label]['option_text'] }}</textarea>
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
                    <div class="fs-13">Objective: 1 jawaban benar, skor otomatis 5/0. Weighted: isi skor masing-masing opsi 1-5.</div>
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
