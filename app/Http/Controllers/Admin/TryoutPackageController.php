<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\TryoutPackage;
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
use ZipArchive;

class TryoutPackageController extends Controller
{
    private const PREVIEW_CACHE_PREFIX = 'tryout_package_import_preview:';
    private const IMPORT_HEADERS = [
        'title',
        'slug',
        'tryout_type',
        'position_target',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'twk_count',
        'tiu_count',
        'tkp_count',
        'teknis_count',
        'manajerial_count',
        'sosiokultural_count',
        'wawancara_count',
    ];

    public function index(Request $request)
    {
        $query = TryoutPackage::query()->latest();

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
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

        $packages = $query->paginate(10)->withQueryString();
        $counts = [
            'all' => TryoutPackage::count(),
            'active' => TryoutPackage::where('is_active', true)->count(),
            'inactive' => TryoutPackage::where('is_active', false)->count(),
        ];
        $questionStockByType = $this->questionStockByType();
        $packageStockStatuses = $packages->getCollection()
            ->mapWithKeys(fn (TryoutPackage $package) => [$package->id => $this->buildPackageStockStatus($package, $questionStockByType)])
            ->all();

        return view('admin.tryout-packages.index', [
            'packages' => $packages,
            'counts' => $counts,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
            'packageStockStatuses' => $packageStockStatuses,
        ]);
    }

    public function importForm(Request $request)
    {
        $previewToken = $request->session()->get('tryout_package_import_preview_token');
        $preview = is_string($previewToken) ? Cache::get($this->previewCacheKey($previewToken)) : null;

        return view('admin.tryout-packages.import', [
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
                'Content-Disposition' => 'attachment; filename="template-import-paket-tryout.xlsx"',
            ]);
        }

        $csv = collect($rows)
            ->map(fn (array $row) => $this->toCsvLine($row))
            ->implode('');

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-paket-tryout.csv"',
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
                'import_file' => 'Tidak ada paket tryout yang bisa diimpor.',
            ]);
        }

        $this->clearImportPreview($request);

        $previewToken = (string) Str::uuid();
        $preview = $this->buildImportPreview($preparedRows, $source, $previewToken);
        Cache::put($this->previewCacheKey($previewToken), $preview, now()->addMinutes(30));
        $request->session()->put('tryout_package_import_preview_token', $previewToken);

        return redirect()
            ->route('admin.tryout-packages.import')
            ->with('success', 'Preview import paket siap. Periksa ringkasan lalu simpan untuk membuat paket tryout.');
    }

    public function create()
    {
        return view('admin.tryout-packages.create', [
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sections($type)])
                ->all(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
            'questionStockByType' => $this->questionStockByType(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePackageInput($request->all());
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['title']);

        $package = TryoutPackage::create($validated);

        ActivityLogger::log('tryout_package.created', $package, 'Admin menambahkan paket tryout.', [
            'title' => $package->title,
        ]);

        return redirect()
            ->route('admin.tryout-packages.index')
            ->with('success', 'Paket tryout berhasil ditambahkan.');
    }

    public function show(TryoutPackage $tryoutPackage)
    {
        return redirect()->route('admin.tryout-packages.edit', $tryoutPackage);
    }

    public function edit(TryoutPackage $tryoutPackage)
    {
        $tryoutPackage->loadCount('sessions');

        return view('admin.tryout-packages.edit', [
            'tryoutPackage' => $tryoutPackage,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sections($type)])
                ->all(),
            'positionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::positionOptions($type)])
                ->all(),
            'questionStockByType' => $this->questionStockByType(),
        ]);
    }

    public function update(Request $request, TryoutPackage $tryoutPackage)
    {
        $validated = $this->validatePackageInput($request->all(), $tryoutPackage);
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['title'], $tryoutPackage->id);

        $tryoutPackage->update($validated);

        ActivityLogger::log('tryout_package.updated', $tryoutPackage, 'Admin memperbarui paket tryout.', [
            'title' => $tryoutPackage->title,
        ]);

        return redirect()
            ->route('admin.tryout-packages.edit', $tryoutPackage)
            ->with('success', 'Paket tryout berhasil diperbarui.');
    }

    public function destroy(TryoutPackage $tryoutPackage)
    {
        $tryoutPackage->delete();

        ActivityLogger::log('tryout_package.deleted', $tryoutPackage, 'Admin menghapus paket tryout.', [
            'title' => $tryoutPackage->title,
        ]);

        return redirect()
            ->route('admin.tryout-packages.index')
            ->with('success', 'Paket tryout berhasil dihapus.');
    }

    private function validatePackageInput(array $input, ?TryoutPackage $tryoutPackage = null): array
    {
        $validated = validator($input, [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tryout_packages', 'slug')->ignore($tryoutPackage?->id)],
            'tryout_type' => ['required', Rule::in(array_keys(TryoutBlueprint::typeOptions()))],
            'position_target' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'section_counts' => ['required', 'array'],
            'is_active' => ['nullable'],
        ])->validate();

        $type = $validated['tryout_type'];
        $validated['position_target'] = TryoutBlueprint::normalizePositionTarget($type, $validated['position_target'] ?? null);

        if (TryoutBlueprint::requiresPositionTarget($type) && $validated['position_target'] === null) {
            throw ValidationException::withMessages([
                'position_target' => 'Target jabatan wajib dipilih untuk paket PPPK Tendik.',
            ]);
        }

        $sections = TryoutBlueprint::sections($type);
        $composition = [];
        $totalQuestions = 0;

        foreach ($sections as $section) {
            $count = (int) data_get($validated['section_counts'], $type . '.' . $section['key'], 0);
            $available = $this->availableQuestionCount($type, $section['key'], $validated['position_target']);

            if ($count > $available) {
                throw ValidationException::withMessages([
                    'section_counts' => "Stok soal untuk {$section['label']} tidak cukup. Diminta {$count}, tersedia {$available} soal aktif.",
                ]);
            }

            $totalQuestions += $count;
            $composition[] = [
                'key' => $section['key'],
                'label' => $section['label'],
                'count' => $count,
                'scoring_mode' => $section['scoring_mode'],
            ];
        }

        if ($totalQuestions < 1) {
            throw ValidationException::withMessages([
                'section_counts' => 'Komposisi soal minimal harus berisi 1 soal.',
            ]);
        }

        $validated['section_composition'] = $composition;
        $validated['section_thresholds'] = TryoutBlueprint::scaledThresholds($type, $composition);
        $validated['twk_count'] = $type === TryoutBlueprint::TYPE_CPNS ? (int) data_get($validated['section_counts'], $type . '.twk', 0) : 0;
        $validated['tiu_count'] = $type === TryoutBlueprint::TYPE_CPNS ? (int) data_get($validated['section_counts'], $type . '.tiu', 0) : 0;
        $validated['tkp_count'] = $type === TryoutBlueprint::TYPE_CPNS ? (int) data_get($validated['section_counts'], $type . '.tkp', 0) : 0;
        $validated['is_active'] = $this->normalizeImportBoolean((string) data_get($input, 'is_active', '1'));

        return $validated;
    }

    private function prepareImportRow(array $row, array $headerMap, int $rowNumber, string $source): array
    {
        $data = [];

        foreach (self::IMPORT_HEADERS as $header) {
            $index = $headerMap[$header] ?? null;
            $data[$header] = $index !== null ? trim((string) ($row[$index] ?? '')) : '';
        }

        $type = TryoutBlueprint::normalizeType($data['tryout_type']);
        $positionTarget = TryoutBlueprint::normalizePositionTarget($type, $data['position_target']);

        if (! array_key_exists($type, TryoutBlueprint::typeOptions())) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: jenis tryout tidak valid.",
            ]);
        }

        if ($data['title'] === '') {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: title wajib diisi.",
            ]);
        }

        if (! is_numeric($data['price']) || (int) $data['price'] < 0) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: price harus angka 0 atau lebih besar.",
            ]);
        }

        if (! is_numeric($data['duration_minutes']) || (int) $data['duration_minutes'] < 1) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: duration_minutes harus angka minimal 1.",
            ]);
        }

        if (TryoutBlueprint::requiresPositionTarget($type) && $positionTarget === null) {
            throw ValidationException::withMessages([
                'import_file' => "Baris {$rowNumber}: position_target wajib diisi untuk paket PPPK Tendik.",
            ]);
        }

        $sectionCounts = [$type => []];

        foreach (TryoutBlueprint::sections($type) as $section) {
            $countKey = $section['key'] . '_count';
            $rawCount = $data[$countKey] ?? '';
            $count = $rawCount === '' ? 0 : (int) $rawCount;

            if ($rawCount !== '' && (! is_numeric($rawCount) || $count < 0)) {
                throw ValidationException::withMessages([
                    'import_file' => "Baris {$rowNumber}: {$countKey} harus angka 0 atau lebih besar.",
                ]);
            }

            $sectionCounts[$type][$section['key']] = $count;
        }

        $validated = $this->validatePackageInput([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'tryout_type' => $type,
            'position_target' => $positionTarget,
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'price' => (int) $data['price'],
            'duration_minutes' => (int) $data['duration_minutes'],
            'is_active' => $data['is_active'],
            'section_counts' => $sectionCounts,
        ]);

        return [
            'package' => $validated,
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

        $createdPackages = DB::transaction(function () use ($preview) {
            return collect($preview['rows'])->map(function (array $row) {
                $payload = $row['package'];
                $payload['slug'] = $this->makeUniqueSlug($payload['slug'] ?: $payload['title']);
                $package = TryoutPackage::create($payload);

                ActivityLogger::log('tryout_package.imported', $package, 'Admin mengimpor paket tryout.', [
                    'title' => $package->title,
                    'source' => $row['source'],
                ]);

                return $package;
            });
        });

        ActivityLogger::log('tryout_package.import.batch', null, 'Admin mengimpor paket tryout.', [
            'count' => $createdPackages->count(),
            'source' => $preview['source'] ?? 'unknown',
        ]);

        Cache::forget($this->previewCacheKey($validated['preview_token']));
        $request->session()->forget('tryout_package_import_preview_token');

        return redirect()
            ->route('admin.tryout-packages.index')
            ->with('success', $createdPackages->count() . ' paket tryout berhasil diimpor.');
    }

    private function buildImportPreview(array $preparedRows, string $source, string $previewToken): array
    {
        $typeSummary = collect($preparedRows)
            ->groupBy(fn (array $row) => $row['package']['tryout_type'])
            ->map(function ($rows, $type) {
                $positions = collect($rows)
                    ->map(fn (array $row) => $row['package']['position_target']
                        ? TryoutBlueprint::positionLabel($row['package']['tryout_type'], $row['package']['position_target'])
                        : 'Umum')
                    ->unique()
                    ->values()
                    ->implode(', ');

                return [
                'type' => $type,
                'type_label' => TryoutBlueprint::typeLabel($type),
                'position_label' => $positions,
                'count' => count($rows),
                ];
            })
            ->values()
            ->all();

        return [
            'token' => $previewToken,
            'source' => $source,
            'count' => count($preparedRows),
            'type_summary' => $typeSummary,
            'rows' => $preparedRows,
            'preview_rows' => collect($preparedRows)
                ->take(5)
                ->map(function (array $row) {
                    $package = $row['package'];

                    return [
                        'title' => $package['title'],
                        'slug' => $package['slug'] ?: Str::slug($package['title']),
                        'tryout_type_label' => TryoutBlueprint::typeLabel($package['tryout_type']),
                        'position_label' => $package['position_target']
                            ? TryoutBlueprint::positionLabel($package['tryout_type'], $package['position_target'])
                            : 'Umum',
                        'duration_minutes' => (int) $package['duration_minutes'],
                        'price' => (int) $package['price'],
                        'is_active' => (bool) $package['is_active'],
                        'composition' => collect($package['section_composition'])
                            ->map(fn (array $section) => $section['label'] . ' ' . $section['count'])
                            ->implode(' · '),
                    ];
                })
                ->all(),
        ];
    }

    private function clearImportPreview(Request $request): void
    {
        $existingToken = $request->session()->pull('tryout_package_import_preview_token');

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
                'Tryout CPNS Paket A',
                '',
                'cpns',
                '',
                'Paket latihan CPNS lengkap.',
                '49000',
                '100',
                '1',
                '30',
                '35',
                '45',
                '',
                '',
                '',
                '',
            ],
            [
                'Tryout PPPK Teknis Dasar',
                '',
                'pppk_tendik',
                'operator_sekolah',
                'Paket latihan PPPK untuk kompetensi dasar.',
                '59000',
                '120',
                '1',
                '',
                '',
                '',
                '40',
                '20',
                '20',
                '10',
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

            return Str::startsWith($target, 'xl/') ? $target : 'xl/' . $target;
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

        $path = tempnam(sys_get_temp_dir(), 'tryout-package-template-');

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
        <sheet name="Paket Tryout" sheetId="1" r:id="rId1"/>
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

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $counter = 2;

        while (
            TryoutPackage::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function questionStockByType(): array
    {
        return collect(TryoutBlueprint::typeOptions())
            ->mapWithKeys(function ($label, $type) {
                $positions = TryoutBlueprint::positionOptions($type);
                $positionBuckets = ['__all__' => null];

                foreach (array_keys($positions) as $positionKey) {
                    $positionBuckets[$positionKey] = $positionKey;
                }

                return [
                    $type => collect($positionBuckets)
                        ->mapWithKeys(fn ($positionTarget, $bucketKey) => [
                            $bucketKey => collect(TryoutBlueprint::sections($type))
                                ->mapWithKeys(fn (array $section) => [
                                    $section['key'] => $this->availableQuestionCount($type, $section['key'], $positionTarget),
                                ])
                                ->all(),
                        ])
                        ->all(),
                ];
            })
            ->all();
    }

    private function availableQuestionCount(string $tryoutType, string $section, ?string $positionTarget = null): int
    {
        $targetSection = strtolower($section);
        $normalizedPositionTarget = TryoutBlueprint::normalizePositionTarget($tryoutType, $positionTarget);

        return Question::query()
            ->where('tryout_type', TryoutBlueprint::normalizeType($tryoutType))
            ->when(
                $normalizedPositionTarget !== null,
                fn ($query) => $query->where('position_target', $normalizedPositionTarget)
            )
            ->where('is_active', true)
            ->get()
            ->filter(fn (Question $question) => strtolower((string) $question->section) === $targetSection)
            ->count();
    }

    private function buildPackageStockStatus(TryoutPackage $package, array $questionStockByType): array
    {
        $sections = collect($package->sectionSummaries())
            ->map(function (array $section) use ($package, $questionStockByType) {
                $positionBucket = $package->position_target ?: '__all__';
                $available = (int) ($questionStockByType[$package->tryout_type][$positionBucket][$section['key']] ?? 0);
                $required = (int) ($section['count'] ?? 0);

                return [
                    'label' => $section['label'],
                    'required' => $required,
                    'available' => $available,
                    'enough' => $available >= $required,
                ];
            })
            ->values();

        return [
            'enough' => $sections->every(fn (array $section) => $section['enough']),
            'sections' => $sections->all(),
        ];
    }
}
