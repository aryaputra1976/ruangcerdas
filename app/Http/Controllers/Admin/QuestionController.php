<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Support\TryoutBlueprint;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use ZipArchive;

class QuestionController extends Controller
{
    private const OPTION_LABELS = ['A', 'B', 'C', 'D', 'E'];
    private const PREVIEW_CACHE_PREFIX = 'question_import_preview:';
    private const IMPORT_HEADERS = [
        'tryout_type',
        'position_target',
        'section',
        'category_slug',
        'difficulty',
        'question_text',
        'explanation',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_option',
        'score_a',
        'score_b',
        'score_c',
        'score_d',
        'score_e',
        'is_active',
    ];

    public function index(Request $request)
    {
        $query = Question::query()
            ->with(['category', 'options'])
            ->latest();

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('question_text', 'like', "%{$q}%")
                    ->orWhere('explanation', 'like', "%{$q}%");
            });
        }

        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('tryout_type')) {
            $query->where('tryout_type', $request->tryout_type);
        }

        if ($request->filled('position_target')) {
            $query->where('position_target', $request->position_target);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('question_category_id')) {
            $query->where('question_category_id', $request->question_category_id);
        }

        $questions = $query->paginate(10)->withQueryString();
        $categories = QuestionCategory::query()->orderBy('tryout_type')->orderBy('section')->orderBy('name')->get();
        $counts = [
            'all' => Question::count(),
            'active' => Question::where('is_active', true)->count(),
            'inactive' => Question::where('is_active', false)->count(),
        ];

        return view('admin.questions.index', [
            'questions' => $questions,
            'categories' => $categories,
            'counts' => $counts,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sectionOptions($type)])
                ->all(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
        ]);
    }

    public function create()
    {
        $categories = QuestionCategory::query()->orderBy('tryout_type')->orderBy('section')->orderBy('name')->get();

        return view('admin.questions.create', [
            'categories' => $categories,
            'optionLabels' => self::OPTION_LABELS,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sectionOptions($type)])
                ->all(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
        ]);
    }

    public function importForm(Request $request)
    {
        $previewToken = $request->session()->get('question_import_preview_token');
        $preview = is_string($previewToken) ? Cache::get($this->previewCacheKey($previewToken)) : null;

        return view('admin.questions.import', [
            'headers' => self::IMPORT_HEADERS,
            'preview' => is_array($preview) ? $preview : null,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sectionOptions($type)])
                ->all(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
        ]);
    }

    public function downloadImportTemplate(): Response
    {
        $rows = $this->templateRows();
        $format = request()->string('format')->lower()->value();

        if ($format === 'xlsx') {
            return response($this->buildSimpleXlsx($rows), 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="template-import-bank-soal.xlsx"',
            ]);
        }

        $csv = collect($rows)
            ->map(fn (array $row) => $this->toCsvLine($row))
            ->implode("");

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-bank-soal.csv"',
        ]);
    }

    public function import(Request $request)
    {
        if ($request->input('action') === 'commit') {
            return $this->commitImportPreview($request);
        }

        $request->validate([
            'import_file' => [
                'required',
                'file',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! $value instanceof \Illuminate\Http\UploadedFile) {
                        $fail('File import tidak valid.');

                        return;
                    }

                    $extension = Str::lower((string) $value->getClientOriginalExtension());

                    if (! in_array($extension, ['csv', 'txt', 'xlsx'], true)) {
                        $fail('File import harus berformat CSV atau Excel (.xlsx).');
                    }
                },
            ],
        ]);

        $file = $request->file('import_file');
        $extension = Str::lower((string) $file->getClientOriginalExtension());
        $rows = $this->readImportRows($file->getRealPath(), $extension);

        if ($rows === []) {
            throw ValidationException::withMessages([
                'import_file' => 'File import kosong atau tidak memiliki header.',
            ]);
        }

        $header = array_shift($rows);
        $normalizedHeader = $this->normalizeImportHeader($header);
        $this->ensureImportHeaders($normalizedHeader);
        $headerMap = array_flip($normalizedHeader);
        $preparedRows = [];
        $errors = [];
        $rowNumber = 1;

        $source = $extension === 'xlsx' ? 'xlsx' : 'csv';

        foreach ($rows as $row) {
            $rowNumber++;

            if ($this->rowIsBlank($row)) {
                continue;
            }

            try {
                $preparedRows[] = $this->prepareImportRow($row, $headerMap, $rowNumber, $source);
            } catch (ValidationException $exception) {
                foreach ($exception->errors() as $messages) {
                    foreach ((array) $messages as $message) {
                        $errors[] = $message;
                    }
                }
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([
                'import_file' => $errors,
            ]);
        }

        if ($preparedRows === []) {
            throw ValidationException::withMessages([
                'import_file' => 'Tidak ada baris soal yang bisa diimpor.',
            ]);
        }

        $this->clearImportPreview($request);

        $previewToken = (string) Str::uuid();
        $preview = $this->buildImportPreview($preparedRows, $source, $previewToken);
        Cache::put($this->previewCacheKey($previewToken), $preview, now()->addMinutes(30));
        $request->session()->put('question_import_preview_token', $previewToken);

        return redirect()
            ->route('admin.questions.import')
            ->with('success', 'Preview import siap. Periksa ringkasan lalu klik simpan untuk memasukkan soal ke bank soal.');
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);
        $options = $this->prepareOptions($request, $validated['tryout_type'], $validated['section']);

        $question = DB::transaction(function () use ($validated, $request, $options) {
            $question = Question::create([
                'question_category_id' => $validated['question_category_id'] ?? null,
                'tryout_type' => $validated['tryout_type'],
                'position_target' => $validated['position_target'] ?? null,
                'section' => $validated['section'],
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'],
                'is_active' => $request->boolean('is_active'),
            ]);

            $question->options()->createMany($options);

            return $question;
        });

        ActivityLogger::log('question.created', $question, 'Admin menambahkan soal tryout.', [
            'section' => $question->section_label,
        ]);

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function show(Question $question)
    {
        return redirect()->route('admin.questions.edit', $question);
    }

    public function edit(Question $question)
    {
        $question->load(['options', 'category']);
        $categories = QuestionCategory::query()->orderBy('tryout_type')->orderBy('section')->orderBy('name')->get();

        return view('admin.questions.edit', [
            'question' => $question,
            'categories' => $categories,
            'optionLabels' => self::OPTION_LABELS,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sectionOptions($type)])
                ->all(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $validated = $this->validateQuestion($request);
        $options = $this->prepareOptions($request, $validated['tryout_type'], $validated['section']);

        DB::transaction(function () use ($validated, $request, $options, $question) {
            $question->update([
                'question_category_id' => $validated['question_category_id'] ?? null,
                'tryout_type' => $validated['tryout_type'],
                'position_target' => $validated['position_target'] ?? null,
                'section' => $validated['section'],
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'],
                'is_active' => $request->boolean('is_active'),
            ]);

            $question->options()->delete();
            $question->options()->createMany($options);
        });

        ActivityLogger::log('question.updated', $question, 'Admin memperbarui soal tryout.', [
            'section' => $question->section_label,
        ]);

        return redirect()
            ->route('admin.questions.edit', $question)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        ActivityLogger::log('question.deleted', $question, 'Admin menghapus soal tryout.', [
            'section' => $question->section_label,
        ]);

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    private function validateQuestion(Request $request): array
    {
        $validator = validator($request->all(), [
            'question_category_id' => ['nullable', 'exists:question_categories,id'],
            'tryout_type' => ['required', Rule::in(array_keys(TryoutBlueprint::typeOptions()))],
            'position_target' => ['nullable', 'string', 'max:100'],
            'section' => ['required', 'string', 'max:100'],
            'question_text' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'options' => ['required', 'array', 'min:4', 'max:5'],
            'options.*.option_text' => ['nullable', 'string'],
            'options.*.score' => ['nullable', 'integer', 'min:0', 'max:5'],
        ]);

        $validator->after(function (Validator $validator) use ($request) {
            $options = $request->input('options', []);
            $section = $request->input('section');
            $tryoutType = $request->input('tryout_type');
            $positionTarget = TryoutBlueprint::normalizePositionTarget($tryoutType, $request->input('position_target'));
            $labels = array_keys($options);

            if (! TryoutBlueprint::isValidSection($tryoutType, $section)) {
                $validator->errors()->add('section', 'Section tidak sesuai dengan jenis tryout yang dipilih.');
            }

            if (TryoutBlueprint::requiresPositionTarget($tryoutType) && $positionTarget === null) {
                $validator->errors()->add('position_target', 'Target jabatan wajib dipilih untuk PPPK Tendik.');
            }

            if ($request->filled('question_category_id')) {
                $category = QuestionCategory::query()->find($request->integer('question_category_id'));

                $categorySection = $category ? Str::lower((string) $category->section) : null;
                $categoryType = $category ? TryoutBlueprint::normalizeType($category->tryout_type) : null;
                $categoryPosition = $category ? TryoutBlueprint::normalizePositionTarget($category->tryout_type, $category->position_target) : null;

                if (
                    $category
                    && (
                        $categorySection !== Str::lower((string) $section)
                        || $categoryType !== $tryoutType
                        || $categoryPosition !== $positionTarget
                    )
                ) {
                    $validator->errors()->add('question_category_id', 'Kategori harus memiliki jenis tryout, target jabatan, dan section yang sama.');
                }
            }

            $scoringMode = TryoutBlueprint::scoringMode($tryoutType, $section);
            $requiredOptionLabels = TryoutBlueprint::optionLabels($tryoutType, $section);
            $requiredOptionLabelText = implode('-', $requiredOptionLabels);

            if (collect($requiredOptionLabels)->diff($labels)->isNotEmpty()) {
                $validator->errors()->add('options', "Soal harus memiliki opsi lengkap {$requiredOptionLabelText}.");
            }

            foreach ($requiredOptionLabels as $label) {
                if (blank(data_get($options, $label . '.option_text'))) {
                    $validator->errors()->add("options.$label.option_text", "Teks opsi {$label} wajib diisi.");
                }
            }

            if ($scoringMode === 'single_correct') {
                $correctCount = collect($requiredOptionLabels)
                    ->filter(fn ($label) => (bool) data_get($options, $label . '.is_correct'))
                    ->count();

                if ($correctCount !== 1) {
                    $validator->errors()->add('options', 'Untuk section dengan jawaban tunggal, pilih tepat satu jawaban benar.');
                }
            }

            if ($scoringMode === 'weighted') {
                $maxWeightedScore = TryoutBlueprint::maxWeightedScore($tryoutType, $section);

                foreach ($requiredOptionLabels as $label) {
                    $score = data_get($options, $label . '.score');

                    if (! is_numeric($score) || (int) $score < 1 || (int) $score > $maxWeightedScore) {
                        $validator->errors()->add("options.$label.score", "Skor opsi {$label} untuk section ini harus 1 sampai {$maxWeightedScore}.");
                    }
                }
            }
        });

        $validated = $validator->validate();
        $validated['tryout_type'] = TryoutBlueprint::normalizeType($validated['tryout_type']);
        $validated['position_target'] = TryoutBlueprint::normalizePositionTarget(
            $validated['tryout_type'],
            $validated['position_target'] ?? null
        );

        return $validated;
    }

    private function prepareOptions(Request $request, string $tryoutType, string $section): array
    {
        $options = [];
        $scoringMode = TryoutBlueprint::scoringMode($tryoutType, $section);
        $requiredOptionLabels = TryoutBlueprint::optionLabels($tryoutType, $section);

        foreach ($requiredOptionLabels as $label) {
            $optionText = trim((string) data_get($request->input('options', []), $label . '.option_text'));
            $isCorrect = (bool) data_get($request->input('options', []), $label . '.is_correct');
            $score = (int) data_get($request->input('options', []), $label . '.score', 0);

            if ($scoringMode === 'single_correct') {
                $score = $isCorrect ? 5 : 0;
            }

            $options[] = [
                'option_label' => $label,
                'option_text' => $optionText,
                'is_correct' => $isCorrect,
                'score' => $score,
            ];
        }

        return $options;
    }

    private function prepareImportRow(array $row, array $headerMap, int $rowNumber, string $source): array
    {
        $data = [];

        foreach (self::IMPORT_HEADERS as $header) {
            $index = $headerMap[$header] ?? null;
            $data[$header] = $index !== null ? trim((string) ($row[$index] ?? '')) : '';
        }

        $tryoutType = TryoutBlueprint::normalizeType($data['tryout_type']);
        $positionTarget = TryoutBlueprint::normalizePositionTarget($tryoutType, $data['position_target']);
        $section = Str::lower($data['section']);
        $difficulty = Str::lower($data['difficulty']);

        if (! array_key_exists($tryoutType, TryoutBlueprint::typeOptions())) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: jenis tryout tidak valid.",
            ]);
        }

        if (! TryoutBlueprint::isValidSection($tryoutType, $section)) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: section tidak sesuai dengan jenis tryout.",
            ]);
        }

        if (TryoutBlueprint::requiresPositionTarget($tryoutType) && $positionTarget === null) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: position_target wajib diisi untuk PPPK Tendik.",
            ]);
        }

        if (! in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: level kesulitan harus easy, medium, atau hard.",
            ]);
        }

        $categoryId = null;

        if ($data['category_slug'] !== '') {
            $category = QuestionCategory::query()
                ->where('slug', $data['category_slug'])
                ->first();

            if (! $category) {
                throw ValidationException::withMessages([
                    'import_file' => "Baris {$rowNumber}: category_slug tidak ditemukan.",
                ]);
            }

            if (
                TryoutBlueprint::normalizeType($category->tryout_type) !== $tryoutType
                || Str::lower((string) $category->section) !== $section
                || TryoutBlueprint::normalizePositionTarget($category->tryout_type, $category->position_target) !== $positionTarget
            ) {
                throw ValidationException::withMessages([
                    'import_file' => "Baris {$rowNumber}: category_slug harus punya jenis tryout, target jabatan, dan section yang sama.",
                ]);
            }

            $categoryId = $category->id;
        }

        if ($data['question_text'] === '') {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: question_text wajib diisi.",
            ]);
        }

        $scoringMode = TryoutBlueprint::scoringMode($tryoutType, $section);
        $requiredOptionLabels = TryoutBlueprint::optionLabels($tryoutType, $section);
        $maxWeightedScore = TryoutBlueprint::maxWeightedScore($tryoutType, $section);
        $correctOption = Str::upper($data['correct_option']);
        $options = [];

        foreach ($requiredOptionLabels as $label) {
            $optionText = $data['option_' . Str::lower($label)] ?? '';

            if ($optionText === '') {
                throw ValidationException::withMessages([
                    'import_file' => "Baris {$rowNumber}: option_{$label} wajib diisi.",
                ]);
            }

            $isCorrect = $correctOption === $label;
            $score = 0;

            if ($scoringMode === 'single_correct') {
                $score = $isCorrect ? 5 : 0;
            } else {
                $rawScore = $data['score_' . Str::lower($label)] ?? '';

                if (! is_numeric($rawScore) || (int) $rawScore < 1 || (int) $rawScore > $maxWeightedScore) {
                    throw ValidationException::withMessages([
                        'import_file' => "Baris {$rowNumber}: score_{$label} harus diisi angka 1 sampai {$maxWeightedScore} untuk section weighted.",
                    ]);
                }

                $score = (int) $rawScore;
                $isCorrect = false;
            }

            $options[] = [
                'option_label' => $label,
                'option_text' => $optionText,
                'is_correct' => $isCorrect,
                'score' => $score,
            ];
        }

        if ($scoringMode === 'single_correct' && ! in_array($correctOption, $requiredOptionLabels, true)) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: correct_option harus salah satu dari " . implode(', ', $requiredOptionLabels) . '.',
            ]);
        }

        return [
            'question' => [
                'question_category_id' => $categoryId,
                'tryout_type' => $tryoutType,
                'position_target' => $positionTarget,
                'section' => $section,
                'question_text' => $data['question_text'],
                'explanation' => $data['explanation'] !== '' ? $data['explanation'] : null,
                'difficulty' => $difficulty,
                'is_active' => $this->normalizeImportBoolean($data['is_active']),
            ],
            'options' => $options,
            'source' => $source,
        ];
    }

    private function commitImportPreview(Request $request)
    {
        $validated = $request->validate([
            'preview_token' => ['required', 'string'],
        ]);

        $preview = Cache::get($this->previewCacheKey($validated['preview_token']));

        if (! is_array($preview) || empty($preview['rows'])) {
            throw ValidationException::withMessages([
                'import_file' => 'Preview import sudah tidak tersedia. Silakan upload ulang file untuk membuat preview baru.',
            ]);
        }

        $createdQuestions = DB::transaction(function () use ($preview) {
            return collect($preview['rows'])->map(function (array $row) {
                $question = Question::create($row['question']);
                $question->options()->createMany($row['options']);

                ActivityLogger::log('question.imported', $question, 'Admin mengimpor soal tryout.', [
                    'section' => $question->section_label,
                    'source' => $row['source'],
                ]);

                return $question;
            });
        });

        ActivityLogger::log('question.import.batch', null, 'Admin mengimpor bank soal tryout.', [
            'count' => $createdQuestions->count(),
            'source' => $preview['source'] ?? 'unknown',
        ]);

        Cache::forget($this->previewCacheKey($validated['preview_token']));
        $request->session()->forget('question_import_preview_token');

        return redirect()
            ->route('admin.questions.index')
            ->with('success', $createdQuestions->count() . ' soal berhasil diimpor.');
    }

    private function buildImportPreview(array $preparedRows, string $source, string $previewToken): array
    {
        $sectionSummary = collect($preparedRows)
            ->groupBy(fn (array $row) => implode(':', [
                $row['question']['tryout_type'],
                $row['question']['position_target'] ?: 'umum',
                $row['question']['section'],
            ]))
            ->map(function ($rows, $key) {
                [$type, $positionTarget, $section] = explode(':', $key, 3);

                return [
                    'type' => $type,
                    'type_label' => TryoutBlueprint::typeLabel($type),
                    'position_label' => $positionTarget !== 'umum' ? TryoutBlueprint::positionLabel($type, $positionTarget) : 'Umum',
                    'section' => $section,
                    'section_label' => TryoutBlueprint::sectionLabel($type, $section),
                    'count' => count($rows),
                ];
            })
            ->values()
            ->all();

        return [
            'token' => $previewToken,
            'source' => $source,
            'count' => count($preparedRows),
            'section_summary' => $sectionSummary,
            'rows' => $preparedRows,
            'preview_rows' => collect($preparedRows)
                ->take(5)
                ->map(fn (array $row) => [
                    'tryout_type_label' => TryoutBlueprint::typeLabel($row['question']['tryout_type']),
                    'position_label' => $row['question']['position_target']
                        ? TryoutBlueprint::positionLabel($row['question']['tryout_type'], $row['question']['position_target'])
                        : 'Umum',
                    'section_label' => TryoutBlueprint::sectionLabel($row['question']['tryout_type'], $row['question']['section']),
                    'difficulty' => ucfirst($row['question']['difficulty']),
                    'question_text' => $row['question']['question_text'],
                    'is_active' => $row['question']['is_active'],
                ])
                ->all(),
        ];
    }

    private function clearImportPreview(Request $request): void
    {
        $existingToken = $request->session()->pull('question_import_preview_token');

        if (is_string($existingToken) && $existingToken !== '') {
            Cache::forget($this->previewCacheKey($existingToken));
        }
    }

    private function previewCacheKey(string $token): string
    {
        return self::PREVIEW_CACHE_PREFIX . $token;
    }

    private function normalizeImportBoolean(string $value): bool
    {
        $normalized = Str::lower(trim($value));

        if ($normalized === '') {
            return true;
        }

        return in_array($normalized, ['1', 'true', 'yes', 'ya', 'aktif'], true);
    }

    private function rowIsBlank(array $row): bool
    {
        return collect($row)->every(fn ($value) => trim((string) $value) === '');
    }

    private function toCsvLine(array $row): string
    {
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, $row);
        rewind($stream);
        $csv = (string) stream_get_contents($stream);
        fclose($stream);

        return $csv;
    }

    private function templateRows(): array
    {
        return [
            self::IMPORT_HEADERS,
            [
                'cpns',
                '',
                'twk',
                '',
                'medium',
                'Pancasila ditetapkan sebagai dasar negara pada tanggal?',
                'Pancasila disahkan pada 18 Agustus 1945.',
                '1 Juni 1945',
                '17 Agustus 1945',
                '18 Agustus 1945',
                '20 Mei 1908',
                '28 Oktober 1928',
                'C',
                '',
                '',
                '',
                '',
                '',
                '1',
            ],
            [
                'cpns',
                '',
                'tkp',
                '',
                'easy',
                'Rekan kerja Anda terlambat menyerahkan tugas tim. Apa respons terbaik?',
                'Pilih respons paling efektif dan kolaboratif.',
                'Membantu menyusun prioritas kerja agar tugas selesai',
                'Menegur di depan tim',
                'Membiarkan tanpa tindak lanjut',
                'Mengambil alih semua tugas tanpa komunikasi',
                'Melaporkan langsung tanpa klarifikasi',
                '',
                '5',
                '3',
                '1',
                '2',
                '4',
                '1',
            ],
            [
                'pppk_tendik',
                'wali_asuh',
                'manajerial',
                '',
                'medium',
                'Bagaimana respons terbaik ketika peserta didik kesulitan mengikuti aturan asrama?',
                'Pilih respons yang paling terarah dan tetap suportif.',
                'Mengajak bicara, menjelaskan aturan, lalu memberi pendampingan.',
                'Membiarkan karena nanti juga paham sendiri.',
                'Langsung memarahi di depan teman-temannya.',
                'Menyerahkan sepenuhnya ke teman sekamar.',
                '',
                '',
                '4',
                '3',
                '1',
                '2',
                '',
                '1',
            ],
        ];
    }

    private function readImportRows(string $path, string $extension): array
    {
        return match ($extension) {
            'xlsx' => $this->readXlsxRows($path),
            default => $this->readCsvRows($path),
        };
    }

    private function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'import_file' => 'File CSV tidak dapat dibaca.',
            ]);
        }

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsxRows(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw ValidationException::withMessages([
                'import_file' => 'Server belum mendukung pembacaan file Excel (.xlsx).',
            ]);
        }

        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages([
                'import_file' => 'File Excel (.xlsx) tidak dapat dibaca.',
            ]);
        }

        $sharedStrings = $this->readXlsxSharedStrings($zip);
        $sheetPath = $this->resolveFirstWorksheetPath($zip);
        $sheetXml = $zip->getFromName($sheetPath);
        $zip->close();

        if ($sheetXml === false) {
            throw ValidationException::withMessages([
                'import_file' => 'Sheet pertama pada file Excel tidak ditemukan.',
            ]);
        }

        $xml = simplexml_load_string($sheetXml);

        if (! $xml || ! isset($xml->sheetData)) {
            throw ValidationException::withMessages([
                'import_file' => 'Isi sheet Excel tidak valid.',
            ]);
        }

        $rows = [];

        foreach ($xml->sheetData->row as $rowNode) {
            $currentRow = [];

            foreach ($rowNode->c as $cell) {
                $reference = (string) ($cell['r'] ?? '');
                $columnIndex = $this->columnReferenceToIndex($reference);
                $currentRow[$columnIndex] = $this->readXlsxCellValue($cell, $sharedStrings);
            }

            if ($currentRow === []) {
                continue;
            }

            ksort($currentRow);
            $lastIndex = (int) array_key_last($currentRow);
            $normalizedRow = [];

            for ($index = 0; $index <= $lastIndex; $index++) {
                $normalizedRow[] = $currentRow[$index] ?? '';
            }

            $rows[] = $normalizedRow;
        }

        return $rows;
    }

    private function readXlsxSharedStrings(ZipArchive $zip): array
    {
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');

        if ($sharedStringsXml === false) {
            return [];
        }

        $xml = simplexml_load_string($sharedStringsXml);

        if (! $xml) {
            return [];
        }

        $sharedStrings = [];

        foreach ($xml->si as $item) {
            if (isset($item->t)) {
                $sharedStrings[] = (string) $item->t;

                continue;
            }

            $text = '';

            foreach ($item->r as $run) {
                $text .= (string) ($run->t ?? '');
            }

            $sharedStrings[] = $text;
        }

        return $sharedStrings;
    }

    private function resolveFirstWorksheetPath(ZipArchive $zip): string
    {
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');

        if ($workbookXml === false || $relsXml === false) {
            throw ValidationException::withMessages([
                'import_file' => 'Struktur workbook Excel tidak lengkap.',
            ]);
        }

        $workbook = simplexml_load_string($workbookXml);
        $relations = simplexml_load_string($relsXml);

        if (! $workbook || ! $relations) {
            throw ValidationException::withMessages([
                'import_file' => 'Struktur workbook Excel tidak valid.',
            ]);
        }

        $sheetNamespaces = $workbook->getNamespaces(true);
        $sheetAttributes = $workbook->sheets?->sheet?->attributes($sheetNamespaces['r'] ?? null);
        $relationshipId = (string) ($sheetAttributes?->id ?? '');

        if ($relationshipId === '') {
            throw ValidationException::withMessages([
                'import_file' => 'Sheet pertama pada workbook Excel tidak ditemukan.',
            ]);
        }

        foreach ($relations->Relationship as $relationship) {
            if ((string) $relationship['Id'] !== $relationshipId) {
                continue;
            }

            $target = trim((string) $relationship['Target'], '/');

            return Str::startsWith($target, 'xl/')
                ? $target
                : 'xl/' . $target;
        }

        throw ValidationException::withMessages([
            'import_file' => 'Relasi sheet Excel tidak ditemukan.',
        ]);
    }

    private function readXlsxCellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) ($cell['t'] ?? '');

        if ($type === 'inlineStr') {
            return trim((string) $cell->is->t);
        }

        if ($type === 's') {
            $sharedStringIndex = (int) ($cell->v ?? 0);

            return trim((string) ($sharedStrings[$sharedStringIndex] ?? ''));
        }

        if (isset($cell->v)) {
            return trim((string) $cell->v);
        }

        return '';
    }

    private function columnReferenceToIndex(string $reference): int
    {
        preg_match('/[A-Z]+/i', $reference, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(0, $index - 1);
    }

    private function normalizeImportHeader(array $header): array
    {
        return array_map(
            fn ($value) => Str::of((string) $value)->trim()->lower()->replace("\u{FEFF}", '')->value(),
            $header
        );
    }

    private function ensureImportHeaders(array $normalizedHeader): void
    {
        $missingHeaders = collect(self::IMPORT_HEADERS)
            ->diff($normalizedHeader)
            ->values()
            ->all();

        if ($missingHeaders === []) {
            return;
        }

        throw ValidationException::withMessages([
            'import_file' => 'Header file import belum lengkap: ' . implode(', ', $missingHeaders),
        ]);
    }

    private function buildSimpleXlsx(array $rows): string
    {
        if (! class_exists(ZipArchive::class)) {
            abort(500, 'Server belum mendukung pembuatan file Excel (.xlsx).');
        }

        $path = tempnam(sys_get_temp_dir(), 'question-template-');

        if ($path === false) {
            abort(500, 'Gagal menyiapkan file template Excel.');
        }

        File::delete($path);
        $xlsxPath = $path . '.xlsx';
        $zip = new ZipArchive();
        $opened = $zip->open($xlsxPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($opened !== true) {
            abort(500, 'Gagal membuat arsip template Excel.');
        }

        $sheetRows = [];

        foreach ($rows as $rowIndex => $row) {
            $cells = '';

            foreach (array_values($row) as $columnIndex => $value) {
                $cellReference = $this->columnIndexToLetters($columnIndex) . ($rowIndex + 1);
                $escapedValue = htmlspecialchars((string) $value, ENT_XML1);
                $cells .= '<c r="' . $cellReference . '" t="inlineStr"><is><t>' . $escapedValue . '</t></is></c>';
            }

            $sheetRows[] = '<row r="' . ($rowIndex + 1) . '">' . $cells . '</row>';
        }

        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML);

        $zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML);

        $zip->addFromString('docProps/core.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>Ruang Cerdas</dc:creator>
    <cp:lastModifiedBy>Ruang Cerdas</cp:lastModifiedBy>
</cp:coreProperties>
XML);

        $zip->addFromString('docProps/app.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>Ruang Cerdas</Application>
</Properties>
XML);

        $zip->addFromString('xl/workbook.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Bank Soal" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML);

        $zip->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML);

        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'
            . implode('', $sheetRows)
            . '</sheetData></worksheet>');

        $zip->close();

        $content = File::get($xlsxPath);
        File::delete($xlsxPath);

        if ($content === false) {
            abort(500, 'Gagal membaca template Excel yang sudah dibuat.');
        }

        return $content;
    }

    private function columnIndexToLetters(int $index): string
    {
        $letters = '';
        $number = $index + 1;

        while ($number > 0) {
            $mod = ($number - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $number = intdiv($number - $mod - 1, 26);
        }

        return $letters;
    }
}
