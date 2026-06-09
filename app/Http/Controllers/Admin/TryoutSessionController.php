<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Models\TryoutSession;
use App\Support\TryoutBlueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TryoutSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = $this->buildFilteredQuery($request)
            ->with(['package'])
            ->withCount('answers')
            ->withCount([
                'answers as answered_count' => fn ($builder) => $builder->whereNotNull('question_option_id'),
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.tryout-sessions.index', [
            'sessions' => $sessions,
            'counts' => $this->buildCounts(),
            'packages' => $this->packageOptions(),
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $sessions = $this->buildFilteredQuery($request)
            ->with(['package'])
            ->withCount([
                'answers',
                'answers as answered_count' => fn ($builder) => $builder->whereNotNull('question_option_id'),
            ])
            ->latest()
            ->get();
        $sectionColumns = $sessions
            ->flatMap(fn (TryoutSession $session) => collect($session->package?->sectionSummaries() ?? [])->pluck('key'))
            ->map(fn ($key) => strtolower((string) $key))
            ->filter()
            ->unique()
            ->values();

        $fileName = 'tryout-sessions-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($sessions, $sectionColumns) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            $headers = [
                'Nama Peserta',
                'Email',
                'Paket',
                'Jenis Tryout',
                'Status',
                'Hasil',
                'Skor Total',
                'Terjawab',
                'Total Soal',
                'Mulai',
                'Selesai',
                'Dibuat',
            ];

            foreach ($sectionColumns as $sectionKey) {
                $headers[] = 'Skor ' . Str::upper($sectionKey);
            }

            fputcsv($handle, $headers);

            foreach ($sessions as $session) {
                $row = [
                    $session->participant_name,
                    $session->participant_email,
                    $session->package?->title,
                    $session->package?->tryout_type_label,
                    $this->statusLabel($session->status),
                    $this->resultLabel($session),
                    (int) $session->total_score,
                    (int) $session->answered_count,
                    (int) $session->answers_count,
                    $session->started_at?->format('Y-m-d H:i:s'),
                    $session->finished_at?->format('Y-m-d H:i:s'),
                    $session->created_at?->format('Y-m-d H:i:s'),
                ];

                foreach ($sectionColumns as $sectionKey) {
                    $row[] = $session->scoreForSection($sectionKey);
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(TryoutSession $tryoutSession)
    {
        $tryoutSession->load([
            'package',
            'answers.question.category',
            'answers.option',
        ]);

        $sections = collect($tryoutSession->package?->sectionSummaries() ?? [])
            ->map(function (array $section) use ($tryoutSession) {
                $sectionKey = strtolower((string) ($section['key'] ?? ''));
                $sectionAnswers = $tryoutSession->answers
                    ->filter(fn ($answer) => strtolower((string) $answer->question?->section) === $sectionKey)
                    ->values();

                return [
                    'key' => $sectionKey,
                    'label' => $section['label'] ?? strtoupper($sectionKey),
                    'required' => (int) ($section['count'] ?? 0),
                    'score' => $tryoutSession->scoreForSection($sectionKey),
                    'answered' => $sectionAnswers->whereNotNull('question_option_id')->count(),
                    'marked' => $sectionAnswers->where('is_marked', true)->count(),
                ];
            })
            ->values();

        return view('admin.tryout-sessions.show', [
            'tryoutSession' => $tryoutSession,
            'sections' => $sections,
            'statusLabel' => $this->statusLabel($tryoutSession->status),
            'resultLabel' => $this->resultLabel($tryoutSession),
        ]);
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = TryoutSession::query();
        $this->applyBaseFilters($query, $request);

        if ($request->filled('result')) {
            $result = $request->input('result');
            $baseQuery = TryoutSession::query();
            $this->applyBaseFilters($baseQuery, $request);

            $sessionIds = $baseQuery
                ->with(['package', 'answers.question'])
                ->where('status', TryoutSession::STATUS_FINISHED)
                ->get()
                ->filter(function (TryoutSession $session) use ($result) {
                    return $result === 'passed'
                        ? $session->isPassed() === true
                        : ($result === 'failed' ? $session->isPassed() === false : true);
                })
                ->pluck('id');

            $query->whereIn('id', $sessionIds->isNotEmpty() ? $sessionIds->all() : [0]);
        }

        return $query;
    }

    private function applyBaseFilters($query, Request $request): void
    {
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));

            $query->where(function ($builder) use ($q) {
                $builder->where('participant_name', 'like', "%{$q}%")
                    ->orWhere('participant_email', 'like', "%{$q}%")
                    ->orWhereHas('package', fn ($packageQuery) => $packageQuery
                        ->where('title', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tryout_type')) {
            $query->whereHas('package', fn ($packageQuery) => $packageQuery
                ->where('tryout_type', $request->input('tryout_type')));
        }

        if ($request->filled('package_id')) {
            $query->where('tryout_package_id', (int) $request->input('package_id'));
        }
    }

    private function buildCounts(): array
    {
        return [
            'all' => TryoutSession::query()->count(),
            'draft' => TryoutSession::query()->where('status', TryoutSession::STATUS_DRAFT)->count(),
            'ongoing' => TryoutSession::query()->where('status', TryoutSession::STATUS_ONGOING)->count(),
            'finished' => TryoutSession::query()->where('status', TryoutSession::STATUS_FINISHED)->count(),
        ];
    }

    private function packageOptions()
    {
        return TryoutPackage::query()
            ->orderBy('title')
            ->get(['id', 'title', 'tryout_type']);
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            TryoutSession::STATUS_DRAFT => 'Draft',
            TryoutSession::STATUS_ONGOING => 'Sedang Berjalan',
            TryoutSession::STATUS_FINISHED => 'Selesai',
            default => ucfirst($status),
        };
    }

    private function resultLabel(TryoutSession $session): string
    {
        return match ($session->isPassed()) {
            true => 'Lulus',
            false => 'Belum Lulus',
            default => $session->isFinished() ? 'Tanpa Ambang Batas' : '-',
        };
    }
}
