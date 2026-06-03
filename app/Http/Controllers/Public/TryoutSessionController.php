<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\TryoutAnswer;
use App\Models\TryoutAccess;
use App\Models\TryoutPackage;
use App\Models\TryoutSession;
use App\Services\TryoutAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TryoutSessionController extends Controller
{
    private const HISTORY_SESSION_KEY = 'public_tryout_session_ids';

    public function begin(Request $request, TryoutPackage $tryoutPackage, TryoutAccessService $tryoutAccessService): RedirectResponse
    {
        abort_unless($tryoutPackage->is_active, 404);

        $validated = $request->validate([
            'participant_name' => ['required', 'string', 'max:255'],
            'participant_email' => $tryoutPackage->is_free
                ? ['nullable', 'email', 'max:255']
                : ['required', 'email', 'max:255'],
        ]);

        $existingSession = $this->findResumableSession($request, $tryoutPackage, $validated);

        if ($existingSession) {
            if ($existingSession->isExpired()) {
                $this->finalizeSession($existingSession->fresh(['answers.question']));

                return redirect()
                    ->route('public.tryout-sessions.result', $existingSession)
                    ->with('success', 'Sesi sebelumnya sudah melewati batas waktu dan otomatis ditutup.');
            }

            $this->rememberSessionId($request, $existingSession->id);

            return redirect()
                ->route('public.tryout-sessions.exam', $existingSession)
                ->with('success', 'Sesi tryout sebelumnya ditemukan. Silakan lanjutkan.');
        }

        $tryoutAccess = null;

        if (! $tryoutPackage->is_free) {
            $tryoutAccess = $tryoutAccessService->findActiveAccessForPackage(
                $tryoutPackage,
                $validated['participant_email'] ?? null
            );

            if (! $tryoutAccess) {
                return redirect()
                    ->route('public.tryouts.buy', $tryoutPackage)
                    ->with('error', 'Silakan beli paket tryout untuk membuka akses.');
            }
        }

        $questionSets = $this->pickQuestionsForPackage($tryoutPackage);

        if ($questionSets['error']) {
            return back()
                ->withInput()
                ->with('error', $questionSets['error']);
        }

        $session = DB::transaction(function () use ($tryoutPackage, $validated, $questionSets, $tryoutAccess, $tryoutAccessService) {
            if ($tryoutAccess) {
                $lockedAccess = TryoutAccess::query()
                    ->whereKey($tryoutAccess->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedAccess || ! $lockedAccess->isCurrentlyActive()) {
                    throw ValidationException::withMessages([
                        'participant_email' => 'Akses tryout premium tidak aktif atau sudah habis.',
                    ]);
                }
            }

            $session = TryoutSession::query()->create([
                'tryout_package_id' => $tryoutPackage->id,
                'participant_name' => $validated['participant_name'],
                'participant_email' => $validated['participant_email'] ?? null,
                'started_at' => now(),
                'duration_minutes' => $tryoutPackage->duration_minutes,
                'status' => TryoutSession::STATUS_ONGOING,
            ]);

            foreach ($questionSets['questions'] as $question) {
                $session->answers()->create([
                    'question_id' => $question->id,
                    'question_option_id' => null,
                    'is_marked' => false,
                    'score' => 0,
                ]);
            }

            if ($tryoutAccess) {
                $tryoutAccessService->consumeAttempt($tryoutAccess);
            }

            return $session;
        });

        $this->rememberSessionId($request, $session->id);

        if (! $tryoutPackage->is_free && ! empty($validated['participant_email'])) {
            $tryoutAccessService->rememberAccess($request, $tryoutPackage, $validated['participant_email']);
        }

        return redirect()->route('public.tryout-sessions.exam', $session);
    }

    public function exam(Request $request, TryoutSession $tryoutSession): View|RedirectResponse
    {
        if ($redirect = $this->ensurePremiumSessionVisibility($request, $tryoutSession)) {
            return $redirect;
        }

        if (blank($tryoutSession->started_at)) {
            $tryoutSession->forceFill([
                'started_at' => $tryoutSession->created_at ?? now(),
            ])->save();
        }

        $tryoutSession->load([
            'package',
            'answers.question.options',
        ]);

        if ($tryoutSession->isFinished()) {
            return redirect()->route('public.tryout-sessions.result', $tryoutSession);
        }

        if ($tryoutSession->isExpired()) {
            $this->finalizeSession($tryoutSession->fresh(['answers.question']));

            return redirect()->route('public.tryout-sessions.result', $tryoutSession);
        }

        $answers = $tryoutSession->answers->values();

        return view('public.tryouts.exam', [
            'tryoutSession' => $tryoutSession,
            'answers' => $answers,
            'startTimestamp' => optional($tryoutSession->started_at)->getTimestampMs(),
            'endTimestamp' => optional($tryoutSession->endsAt())->getTimestampMs(),
            'serverNowTimestamp' => now()->getTimestampMs(),
        ]);
    }

    public function history(Request $request): View
    {
        $sessionIds = $this->getHistorySessionIds($request);

        $sessions = TryoutSession::query()
            ->with('package')
            ->whereIn('id', $sessionIds)
            ->latest('started_at')
            ->get()
            ->map(function (TryoutSession $session) {
                if (! $session->isFinished() && $session->isExpired()) {
                    $this->finalizeSession($session->fresh(['answers.question']));

                    return $session->fresh('package');
                }

                return $session;
            });

        return view('public.tryouts.history', [
            'sessions' => $sessions,
        ]);
    }

    public function save(Request $request, TryoutSession $tryoutSession): RedirectResponse|JsonResponse
    {
        if ($redirect = $this->ensurePremiumSessionVisibility($request, $tryoutSession)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'redirect' => $redirect->getTargetUrl(),
                ], 403);
            }

            return $redirect;
        }

        if ($tryoutSession->isFinished()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'redirect' => route('public.tryout-sessions.result', $tryoutSession),
                ], 409);
            }

            return redirect()->route('public.tryout-sessions.result', $tryoutSession);
        }

        $this->persistAnswers($request, $tryoutSession);

        if ($tryoutSession->fresh()->isExpired()) {
            $this->finalizeSession($tryoutSession->fresh(['answers.question']));

            if ($request->expectsJson()) {
                return response()->json([
                    'expired' => true,
                    'redirect' => route('public.tryout-sessions.result', $tryoutSession),
                    'message' => 'Waktu tryout habis. Jawaban otomatis disubmit.',
                ]);
            }

            return redirect()->route('public.tryout-sessions.result', $tryoutSession)
                ->with('success', 'Waktu tryout habis. Jawaban otomatis disubmit.');
        }

        if ($request->expectsJson()) {
            $tryoutSession->load('answers');

            return response()->json([
                'saved' => true,
                'message' => 'Jawaban berhasil disimpan.',
                'summary' => [
                    'answered' => $tryoutSession->answers->whereNotNull('question_option_id')->count(),
                    'marked' => $tryoutSession->answers->where('is_marked', true)->count(),
                    'total' => $tryoutSession->answers->count(),
                ],
            ]);
        }

        return redirect()
            ->route('public.tryout-sessions.exam', $tryoutSession)
            ->with('success', 'Jawaban berhasil disimpan.');
    }

    public function submit(Request $request, TryoutSession $tryoutSession): RedirectResponse
    {
        if ($redirect = $this->ensurePremiumSessionVisibility($request, $tryoutSession)) {
            return $redirect;
        }

        if ($tryoutSession->isFinished()) {
            return redirect()->route('public.tryout-sessions.result', $tryoutSession);
        }

        $this->persistAnswers($request, $tryoutSession);
        $this->finalizeSession($tryoutSession->fresh(['answers.question']));

        return redirect()->route('public.tryout-sessions.result', $tryoutSession);
    }

    public function result(Request $request, TryoutSession $tryoutSession): View|RedirectResponse
    {
        if ($redirect = $this->ensurePremiumSessionVisibility($request, $tryoutSession)) {
            return $redirect;
        }

        if (! $tryoutSession->isFinished() && $tryoutSession->isExpired()) {
            $this->finalizeSession($tryoutSession->fresh(['answers.question']));
            $tryoutSession = $tryoutSession->fresh();
        }

        if (! $tryoutSession->isFinished()) {
            return redirect()->route('public.tryout-sessions.exam', $tryoutSession);
        }

        $tryoutSession->load([
            'package',
            'answers.question.options',
            'answers.option',
        ]);

        $twkCorrect = $tryoutSession->answers
            ->filter(fn (TryoutAnswer $answer) => $answer->question?->section === 'TWK')
            ->filter(fn (TryoutAnswer $answer) => $answer->score === 5)
            ->count();

        $tiuCorrect = $tryoutSession->answers
            ->filter(fn (TryoutAnswer $answer) => $answer->question?->section === 'TIU')
            ->filter(fn (TryoutAnswer $answer) => $answer->score === 5)
            ->count();

        $twkCount = $tryoutSession->answers->filter(fn (TryoutAnswer $answer) => $answer->question?->section === 'TWK')->count();
        $tiuCount = $tryoutSession->answers->filter(fn (TryoutAnswer $answer) => $answer->question?->section === 'TIU')->count();

        $thresholds = [
            'twk' => 65,
            'tiu' => 80,
            'tkp' => 166,
            'total' => 311,
        ];

        $isPassed = $tryoutSession->twk_score >= $thresholds['twk']
            && $tryoutSession->tiu_score >= $thresholds['tiu']
            && $tryoutSession->tkp_score >= $thresholds['tkp']
            && $tryoutSession->total_score >= $thresholds['total'];

        return view('public.tryouts.result', compact(
            'tryoutSession',
            'thresholds',
            'isPassed',
            'twkCorrect',
            'tiuCorrect',
            'twkCount',
            'tiuCount',
        ));
    }

    public function review(Request $request, TryoutSession $tryoutSession): View|RedirectResponse
    {
        if ($redirect = $this->ensurePremiumSessionVisibility($request, $tryoutSession)) {
            return $redirect;
        }

        if (! $tryoutSession->isFinished()) {
            return redirect()->route('public.tryout-sessions.exam', $tryoutSession);
        }

        $tryoutSession->load([
            'package',
            'answers.question.options',
            'answers.option',
        ]);

        $answers = $tryoutSession->answers->values();

        return view('public.tryouts.review', compact('tryoutSession', 'answers'));
    }

    private function findResumableSession(Request $request, TryoutPackage $tryoutPackage, array $validated): ?TryoutSession
    {
        $sessionIds = $this->getHistorySessionIds($request);

        if ($sessionIds === []) {
            return null;
        }

        return TryoutSession::query()
            ->whereIn('id', $sessionIds)
            ->where('tryout_package_id', $tryoutPackage->id)
            ->where('status', TryoutSession::STATUS_ONGOING)
            ->where('participant_name', $validated['participant_name'])
            ->where(function ($query) use ($validated) {
                $email = $validated['participant_email'] ?? null;

                if (blank($email)) {
                    $query->whereNull('participant_email');

                    return;
                }

                $query->where('participant_email', $email);
            })
            ->latest('started_at')
            ->first();
    }

    private function rememberSessionId(Request $request, int $sessionId): void
    {
        $sessionIds = collect($request->session()->get(self::HISTORY_SESSION_KEY, []))
            ->prepend($sessionId)
            ->unique()
            ->take(15)
            ->values()
            ->all();

        $request->session()->put(self::HISTORY_SESSION_KEY, $sessionIds);
    }

    private function getHistorySessionIds(Request $request): array
    {
        return collect($request->session()->get(self::HISTORY_SESSION_KEY, []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function pickQuestionsForPackage(TryoutPackage $tryoutPackage): array
    {
        $requirements = [
            'TWK' => $tryoutPackage->twk_count,
            'TIU' => $tryoutPackage->tiu_count,
            'TKP' => $tryoutPackage->tkp_count,
        ];

        $selectedQuestions = collect();

        foreach ($requirements as $section => $count) {
            if ($count < 1) {
                continue;
            }

            $questions = Question::query()
                ->active()
                ->where('section', $section)
                ->has('options', '>=', 5)
                ->inRandomOrder()
                ->limit($count)
                ->get();

            if ($questions->count() < $count) {
                return [
                    'error' => "Soal aktif section {$section} belum cukup. Dibutuhkan {$count} soal, tersedia {$questions->count()} soal.",
                    'questions' => collect(),
                ];
            }

            $selectedQuestions = $selectedQuestions->merge($questions);
        }

        return [
            'error' => null,
            'questions' => $selectedQuestions->shuffle()->values(),
        ];
    }

    private function persistAnswers(Request $request, TryoutSession $tryoutSession): void
    {
        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'integer'],
            'marked' => ['nullable', 'array'],
            'marked.*' => ['nullable', 'in:1'],
        ]);

        $tryoutSession->loadMissing(['answers.question.options']);

        DB::transaction(function () use ($validated, $tryoutSession) {
            foreach ($tryoutSession->answers as $answer) {
                $selectedOptionId = (int) data_get($validated, 'answers.' . $answer->id, 0);
                $marked = array_key_exists((string) $answer->id, $validated['marked'] ?? []);
                $selectedOption = $answer->question?->options->firstWhere('id', $selectedOptionId);

                $answer->update([
                    'question_option_id' => $selectedOption?->id,
                    'is_marked' => $marked,
                    'score' => $selectedOption?->score ?? 0,
                ]);
            }
        });
    }

    private function finalizeSession(TryoutSession $tryoutSession): void
    {
        if ($tryoutSession->isFinished()) {
            return;
        }

        $tryoutSession->loadMissing('answers.question');

        $scores = [
            'TWK' => 0,
            'TIU' => 0,
            'TKP' => 0,
        ];

        foreach ($tryoutSession->answers as $answer) {
            $section = $answer->question?->section;

            if ($section && array_key_exists($section, $scores)) {
                $scores[$section] += (int) $answer->score;
            }
        }

        $tryoutSession->update([
            'finished_at' => now(),
            'status' => TryoutSession::STATUS_FINISHED,
            'twk_score' => $scores['TWK'],
            'tiu_score' => $scores['TIU'],
            'tkp_score' => $scores['TKP'],
            'total_score' => $scores['TWK'] + $scores['TIU'] + $scores['TKP'],
        ]);
    }

    private function ensurePremiumSessionVisibility(Request $request, TryoutSession $tryoutSession): ?RedirectResponse
    {
        $tryoutSession->loadMissing('package');

        if (! $tryoutSession->package || $tryoutSession->package->is_free) {
            return null;
        }

        $sessionIds = $this->getHistorySessionIds($request);

        if (in_array((int) $tryoutSession->id, $sessionIds, true)) {
            return null;
        }

        return redirect()
            ->route('public.tryouts.index')
            ->with('error', 'Akses sesi tryout premium ini tidak ditemukan di browser kamu.');
    }
}
