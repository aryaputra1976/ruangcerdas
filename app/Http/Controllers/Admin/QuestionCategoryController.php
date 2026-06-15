<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use App\Support\TryoutBlueprint;
use App\Support\ActivityLogger;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class QuestionCategoryController extends Controller
{
    private const PREVIEW_CACHE_PREFIX = 'question_category_import_preview:';
    private const IMPORT_HEADERS = [
        'name',
        'slug',
        'tryout_type',
        'position_target',
        'section',
        'description',
        'is_active',
    ];
    private const OPTIONAL_IMPORT_HEADERS = [
        'position_target',
    ];

    public function index(Request $request)
    {
        $query = QuestionCategory::query()
            ->withCount('questions')
            ->latest();

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('tryout_type')) {
            $query->where('tryout_type', $request->tryout_type);
        }

        if ($request->filled('position_target')) {
            $query->where('position_target', $request->position_target);
        }

        $categories = $query->paginate(10)->withQueryString();
        $counts = [
            'all' => QuestionCategory::count(),
            'active' => QuestionCategory::where('is_active', true)->count(),
            'inactive' => QuestionCategory::where('is_active', false)->count(),
        ];

        return view('admin.question-categories.index', [
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
        return view('admin.question-categories.create', [
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
        $previewToken = $request->session()->get('question_category_import_preview_token');
        $preview = is_string($previewToken) ? Cache::get($this->previewCacheKey($previewToken)) : null;

        return view('admin.question-categories.import', [
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
                'Content-Disposition' => 'attachment; filename="template-import-kategori-soal.xlsx"',
            ]);
        }

        $csv = collect($rows)->map(fn (array $row) => $this->toCsvLine($row))->implode('');

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-kategori-soal.csv"',
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
                'import_file' => 'Tidak ada kategori yang bisa diimpor.',
            ]);
        }

        $this->clearImportPreview($request);
        $previewToken = (string) Str::uuid();
        $preview = $this->buildImportPreview($preparedRows, $source, $previewToken);
        Cache::put($this->previewCacheKey($previewToken), $preview, now()->addMinutes(30));
        $request->session()->put('question_category_import_preview_token', $previewToken);

        return redirect()
            ->route('admin.question-categories.import')
            ->with('success', 'Preview kategori siap. Periksa ringkasan lalu klik simpan untuk memasukkan kategori.');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category = QuestionCategory::create($validated);

        ActivityLogger::log('question_category.created', $category, 'Admin menambahkan kategori soal.', [
            'name' => $category->name,
        ]);

        return redirect()
            ->route('admin.question-categories.index')
            ->with('success', 'Kategori soal berhasil ditambahkan.');
    }

    public function show(QuestionCategory $questionCategory)
    {
        return redirect()->route('admin.question-categories.edit', $questionCategory);
    }

    public function edit(QuestionCategory $questionCategory)
    {
        $questionCategory->loadCount('questions');

        return view('admin.question-categories.edit', [
            'questionCategory' => $questionCategory,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sectionOptions($type)])
                ->all(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
        ]);
    }

    public function update(Request $request, QuestionCategory $questionCategory)
    {
        $validated = $this->validateCategory($request, $questionCategory);
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['name'], $questionCategory->id);
        $validated['is_active'] = $request->boolean('is_active');

        $questionCategory->update($validated);

        ActivityLogger::log('question_category.updated', $questionCategory, 'Admin memperbarui kategori soal.', [
            'name' => $questionCategory->name,
        ]);

        return redirect()
            ->route('admin.question-categories.edit', $questionCategory)
            ->with('success', 'Kategori soal berhasil diperbarui.');
    }

    public function destroy(QuestionCategory $questionCategory)
    {
        if ($questionCategory->questions()->exists()) {
            return redirect()
                ->route('admin.question-categories.index')
                ->with('error', 'Kategori soal tidak bisa dihapus karena masih memiliki soal.');
        }

        $questionCategory->delete();

        ActivityLogger::log('question_category.deleted', $questionCategory, 'Admin menghapus kategori soal.', [
            'name' => $questionCategory->name,
        ]);

        return redirect()
            ->route('admin.question-categories.index')
            ->with('success', 'Kategori soal berhasil dihapus.');
    }

    private function validateCategory(Request $request, ?QuestionCategory $questionCategory = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('question_categories', 'slug')->ignore($questionCategory?->id)],
            'tryout_type' => ['required', Rule::in(array_keys(TryoutBlueprint::typeOptions()))],
            'position_target' => ['nullable', 'string', 'max:100'],
            'section' => ['required', 'string', 'max:100', function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                if (! TryoutBlueprint::isValidSection($request->input('tryout_type'), (string) $value)) {
                    $fail('Section tidak sesuai dengan jenis tryout yang dipilih.');
                }
            }],
            'description' => ['nullable', 'string'],
        ]);

        $validated['tryout_type'] = TryoutBlueprint::normalizeType($validated['tryout_type']);
        $validated['position_target'] = TryoutBlueprint::normalizePositionTarget(
            $validated['tryout_type'],
            $validated['position_target'] ?? null
        );

        if (TryoutBlueprint::requiresPositionTarget($validated['tryout_type']) && $validated['position_target'] === null) {
            throw ValidationException::withMessages([
                'position_target' => 'Target jabatan wajib dipilih untuk PPPK Tendik.',
            ]);
        }

        return $validated;
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $counter = 2;

        while (
            QuestionCategory::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function commitImportPreview(Request $request)
    {
        $validated = $request->validate([
            'preview_token' => ['required', 'string'],
        ]);

        $preview = Cache::get($this->previewCacheKey($validated['preview_token']));

        if (! is_array($preview) || empty($preview['rows'])) {
            throw ValidationException::withMessages([
                'import_file' => 'Preview kategori sudah tidak tersedia. Silakan upload ulang file untuk membuat preview baru.',
            ]);
        }

        $createdCategories = collect($preview['rows'])->map(function (array $row) {
            $category = QuestionCategory::create($row['category']);

            ActivityLogger::log('question_category.imported', $category, 'Admin mengimpor kategori soal.', [
                'name' => $category->name,
                'source' => $row['source'],
            ]);

            return $category;
        });

        ActivityLogger::log('question_category.import.batch', null, 'Admin mengimpor kategori soal.', [
            'count' => $createdCategories->count(),
            'source' => $preview['source'] ?? 'unknown',
        ]);

        Cache::forget($this->previewCacheKey($validated['preview_token']));
        $request->session()->forget('question_category_import_preview_token');

        return redirect()
            ->route('admin.question-categories.index')
            ->with('success', $createdCategories->count() . ' kategori soal berhasil diimpor.');
    }

    private function prepareImportRow(array $row, array $headerMap, int $rowNumber, string $source): array
    {
        $data = [];

        foreach (self::IMPORT_HEADERS as $header) {
            $index = $headerMap[$header] ?? null;
            $data[$header] = $index !== null ? trim((string) ($row[$index] ?? '')) : '';
        }

        if ($data['name'] === '') {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: name wajib diisi.",
            ]);
        }

        $tryoutType = TryoutBlueprint::normalizeType($data['tryout_type']);
        $positionTarget = TryoutBlueprint::normalizePositionTarget($tryoutType, $data['position_target']);
        $section = Str::lower($data['section']);

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

        $slugInput = $data['slug'] !== '' ? $data['slug'] : $data['name'];
        $slug = $this->makeUniqueSlug($slugInput);

        return [
            'category' => [
                'name' => $data['name'],
                'slug' => $slug,
                'tryout_type' => $tryoutType,
                'position_target' => $positionTarget,
                'section' => $section,
                'description' => $data['description'] !== '' ? $data['description'] : null,
                'is_active' => $this->normalizeImportBoolean($data['is_active']),
            ],
            'source' => $source,
        ];
    }

    private function buildImportPreview(array $preparedRows, string $source, string $previewToken): array
    {
        $sectionSummary = collect($preparedRows)
            ->groupBy(fn (array $row) => implode(':', [
                $row['category']['tryout_type'],
                $row['category']['position_target'] ?: 'umum',
                $row['category']['section'],
            ]))
            ->map(function ($rows, $key) {
                [$type, $positionTarget, $section] = explode(':', $key, 3);

                return [
                    'type_label' => TryoutBlueprint::typeLabel($type),
                    'position_label' => $positionTarget !== 'umum' ? TryoutBlueprint::positionLabel($type, $positionTarget) : 'Umum',
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
            'rows' => $preparedRows,
            'section_summary' => $sectionSummary,
            'preview_rows' => collect($preparedRows)->take(5)->map(fn (array $row) => [
                'name' => $row['category']['name'],
                'slug' => $row['category']['slug'],
                'tryout_type_label' => TryoutBlueprint::typeLabel($row['category']['tryout_type']),
                'position_label' => $row['category']['position_target']
                    ? TryoutBlueprint::positionLabel($row['category']['tryout_type'], $row['category']['position_target'])
                    : 'Umum',
                'section_label' => TryoutBlueprint::sectionLabel($row['category']['tryout_type'], $row['category']['section']),
                'is_active' => $row['category']['is_active'],
            ])->all(),
        ];
    }

    private function clearImportPreview(Request $request): void
    {
        $existingToken = $request->session()->pull('question_category_import_preview_token');

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

    private function templateRows(): array
    {
        return [
            self::IMPORT_HEADERS,
            ['TWK Nasional', 'twk-nasional', 'cpns', '', 'twk', 'Kategori materi wawasan kebangsaan.', '1'],
            ['Manajerial Wali Asuh', 'manajerial-wali-asuh', 'pppk_tendik', 'wali_asuh', 'manajerial', 'Kategori latihan manajerial untuk Wali Asuh.', '1'],
        ];
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
            ->reject(fn (string $header) => in_array($header, self::OPTIONAL_IMPORT_HEADERS, true))
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

        $path = tempnam(sys_get_temp_dir(), 'question-category-template-');

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
        <sheet name="Kategori Soal" sheetId="1" r:id="rId1"/>
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

    private function rowIsBlank(array $row): bool
    {
        return collect($row)->every(fn ($value) => trim((string) $value) === '');
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
