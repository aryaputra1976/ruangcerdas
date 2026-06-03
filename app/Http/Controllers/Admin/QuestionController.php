<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class QuestionController extends Controller
{
    private const OPTION_LABELS = ['A', 'B', 'C', 'D', 'E'];

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

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('question_category_id')) {
            $query->where('question_category_id', $request->question_category_id);
        }

        $questions = $query->paginate(10)->withQueryString();
        $categories = QuestionCategory::query()->orderBy('section')->orderBy('name')->get();
        $counts = [
            'all' => Question::count(),
            'active' => Question::where('is_active', true)->count(),
            'inactive' => Question::where('is_active', false)->count(),
        ];

        return view('admin.questions.index', compact('questions', 'categories', 'counts'));
    }

    public function create()
    {
        $categories = QuestionCategory::query()->orderBy('section')->orderBy('name')->get();

        return view('admin.questions.create', [
            'categories' => $categories,
            'optionLabels' => self::OPTION_LABELS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);
        $options = $this->prepareOptions($request, $validated['section']);

        $question = DB::transaction(function () use ($validated, $request, $options) {
            $question = Question::create([
                'question_category_id' => $validated['question_category_id'] ?? null,
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
            'section' => $question->section,
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
        $categories = QuestionCategory::query()->orderBy('section')->orderBy('name')->get();

        return view('admin.questions.edit', [
            'question' => $question,
            'categories' => $categories,
            'optionLabels' => self::OPTION_LABELS,
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $validated = $this->validateQuestion($request);
        $options = $this->prepareOptions($request, $validated['section']);

        DB::transaction(function () use ($validated, $request, $options, $question) {
            $question->update([
                'question_category_id' => $validated['question_category_id'] ?? null,
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
            'section' => $question->section,
        ]);

        return redirect()
            ->route('admin.questions.edit', $question)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        ActivityLogger::log('question.deleted', $question, 'Admin menghapus soal tryout.', [
            'section' => $question->section,
        ]);

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    private function validateQuestion(Request $request): array
    {
        $validator = validator($request->all(), [
            'question_category_id' => ['nullable', 'exists:question_categories,id'],
            'section' => ['required', Rule::in(['TWK', 'TIU', 'TKP'])],
            'question_text' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'options' => ['required', 'array', 'size:5'],
            'options.*.option_text' => ['required', 'string'],
            'options.*.score' => ['nullable', 'integer', 'min:0', 'max:5'],
        ]);

        $validator->after(function (Validator $validator) use ($request) {
            $options = $request->input('options', []);
            $section = $request->input('section');
            $labels = array_keys($options);

            if (collect(self::OPTION_LABELS)->diff($labels)->isNotEmpty()) {
                $validator->errors()->add('options', 'Soal harus memiliki 5 opsi lengkap A sampai E.');
            }

            if ($request->filled('question_category_id')) {
                $category = QuestionCategory::query()->find($request->integer('question_category_id'));

                if ($category && $category->section !== $section) {
                    $validator->errors()->add('question_category_id', 'Section kategori harus sama dengan section soal.');
                }
            }

            if (in_array($section, ['TWK', 'TIU'], true)) {
                $correctCount = collect(self::OPTION_LABELS)
                    ->filter(fn ($label) => (bool) data_get($options, $label . '.is_correct'))
                    ->count();

                if ($correctCount !== 1) {
                    $validator->errors()->add('options', 'Untuk TWK/TIU, pilih tepat satu jawaban benar.');
                }
            }

            if ($section === 'TKP') {
                foreach (self::OPTION_LABELS as $label) {
                    $score = data_get($options, $label . '.score');

                    if (! is_numeric($score) || (int) $score < 1 || (int) $score > 5) {
                        $validator->errors()->add("options.$label.score", "Skor opsi {$label} untuk TKP harus 1 sampai 5.");
                    }
                }
            }
        });

        return $validator->validate();
    }

    private function prepareOptions(Request $request, string $section): array
    {
        $options = [];

        foreach (self::OPTION_LABELS as $label) {
            $optionText = trim((string) data_get($request->input('options', []), $label . '.option_text'));
            $isCorrect = (bool) data_get($request->input('options', []), $label . '.is_correct');
            $score = (int) data_get($request->input('options', []), $label . '.score', 0);

            if (in_array($section, ['TWK', 'TIU'], true)) {
                $score = $isCorrect ? 5 : 0;
            }

            $options[] = [
                'option_label' => $label,
                'option_text' => $optionText,
                'is_correct' => $section === 'TKP' ? $isCorrect : $isCorrect,
                'score' => $section === 'TKP' ? $score : $score,
            ];
        }

        return $options;
    }
}
