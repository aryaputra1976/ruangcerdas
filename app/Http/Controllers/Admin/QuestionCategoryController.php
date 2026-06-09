<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use App\Support\TryoutBlueprint;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class QuestionCategoryController extends Controller
{
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
        ]);
    }

    public function create()
    {
        return view('admin.question-categories.create', [
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'sectionsByType' => collect(TryoutBlueprint::typeOptions())
                ->mapWithKeys(fn ($label, $type) => [$type => TryoutBlueprint::sectionOptions($type)])
                ->all(),
        ]);
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
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('question_categories', 'slug')->ignore($questionCategory?->id)],
            'tryout_type' => ['required', Rule::in(array_keys(TryoutBlueprint::typeOptions()))],
            'section' => ['required', 'string', 'max:100', function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                if (! TryoutBlueprint::isValidSection($request->input('tryout_type'), (string) $value)) {
                    $fail('Section tidak sesuai dengan jenis tryout yang dipilih.');
                }
            }],
            'description' => ['nullable', 'string'],
        ]);
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
}
