<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Support\TryoutBlueprint;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TryoutPackageController extends Controller
{
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

        $packages = $query->paginate(10)->withQueryString();
        $counts = [
            'all' => TryoutPackage::count(),
            'active' => TryoutPackage::where('is_active', true)->count(),
            'inactive' => TryoutPackage::where('is_active', false)->count(),
        ];

        return view('admin.tryout-packages.index', [
            'packages' => $packages,
            'counts' => $counts,
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
        ]);
    }

    public function create()
    {
        return view('admin.tryout-packages.create', [
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sections($type)])
                ->all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePackage($request);
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['title']);
        $validated['is_active'] = $request->boolean('is_active');

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
        ]);
    }

    public function update(Request $request, TryoutPackage $tryoutPackage)
    {
        $validated = $this->validatePackage($request, $tryoutPackage);
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['title'], $tryoutPackage->id);
        $validated['is_active'] = $request->boolean('is_active');

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

    private function validatePackage(Request $request, ?TryoutPackage $tryoutPackage = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tryout_packages', 'slug')->ignore($tryoutPackage?->id)],
            'tryout_type' => ['required', Rule::in(array_keys(TryoutBlueprint::typeOptions()))],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'section_counts' => ['required', 'array'],
        ]);

        $type = $validated['tryout_type'];
        $sections = TryoutBlueprint::sections($type);
        $composition = [];
        $totalQuestions = 0;

        foreach ($sections as $section) {
            $count = (int) data_get($request->input('section_counts', []), $type . '.' . $section['key'], 0);
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
        $validated['section_thresholds'] = TryoutBlueprint::defaultThresholds($type);
        $validated['twk_count'] = $type === TryoutBlueprint::TYPE_CPNS ? (int) data_get($request->input('section_counts', []), $type . '.twk', 0) : 0;
        $validated['tiu_count'] = $type === TryoutBlueprint::TYPE_CPNS ? (int) data_get($request->input('section_counts', []), $type . '.tiu', 0) : 0;
        $validated['tkp_count'] = $type === TryoutBlueprint::TYPE_CPNS ? (int) data_get($request->input('section_counts', []), $type . '.tkp', 0) : 0;

        return $validated;
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
}
