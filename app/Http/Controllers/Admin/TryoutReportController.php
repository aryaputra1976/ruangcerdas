<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Models\TryoutSession;
use App\Support\TryoutBlueprint;
use Illuminate\Http\Request;

class TryoutReportController extends Controller
{
    public function index(Request $request)
    {
        $sessions = $this->buildFilteredQuery($request)
            ->with('package')
            ->latest('finished_at')
            ->latest()
            ->get();

        $packageReports = $sessions
            ->groupBy('tryout_package_id')
            ->map(function ($group) {
                /** @var \App\Models\TryoutSession $first */
                $first = $group->first();
                $package = $first?->package;
                $passedCount = $group->filter(fn (TryoutSession $session) => $session->isPassed() === true)->count();

                return (object) [
                    'package' => $package,
                    'sessions_count' => $group->count(),
                    'participants_count' => $group
                        ->map(fn (TryoutSession $session) => strtolower(trim((string) ($session->participant_email ?: $session->participant_name))))
                        ->filter()
                        ->unique()
                        ->count(),
                    'average_score' => round((float) $group->avg('total_score'), 1),
                    'highest_score' => (int) $group->max('total_score'),
                    'passed_count' => $passedCount,
                    'pass_rate' => $group->count() > 0 ? round(($passedCount / $group->count()) * 100, 1) : 0.0,
                    'latest_finished_at' => $group->max('finished_at'),
                ];
            })
            ->filter(fn (object $report) => $report->package !== null)
            ->sortByDesc('sessions_count')
            ->sortByDesc('average_score')
            ->values();

        $topParticipants = $sessions
            ->sort(function (TryoutSession $left, TryoutSession $right) {
                if ((int) $left->total_score === (int) $right->total_score) {
                    return ($right->finished_at?->getTimestamp() ?? 0) <=> ($left->finished_at?->getTimestamp() ?? 0);
                }

                return (int) $right->total_score <=> (int) $left->total_score;
            })
            ->take(10)
            ->values();

        $passedCount = $sessions->filter(fn (TryoutSession $session) => $session->isPassed() === true)->count();

        return view('admin.tryout-reports.index', [
            'packages' => TryoutPackage::query()->orderBy('title')->get(['id', 'title', 'tryout_type']),
            'tryoutTypes' => TryoutBlueprint::typeOptions(),
            'selectedPackage' => $request->filled('package_id')
                ? TryoutPackage::query()->find((int) $request->input('package_id'))
                : null,
            'sessionsCount' => $sessions->count(),
            'participantsCount' => $sessions
                ->map(fn (TryoutSession $session) => strtolower(trim((string) ($session->participant_email ?: $session->participant_name))))
                ->filter()
                ->unique()
                ->count(),
            'averageScore' => round((float) $sessions->avg('total_score'), 1),
            'passRate' => $sessions->count() > 0 ? round(($passedCount / $sessions->count()) * 100, 1) : 0.0,
            'topParticipants' => $topParticipants,
            'packageReports' => $packageReports,
        ]);
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = TryoutSession::query()
            ->where('status', TryoutSession::STATUS_FINISHED)
            ->whereHas('package');

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

        if ($request->filled('from')) {
            $query->whereDate('finished_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('finished_at', '<=', $request->input('to'));
        }

        if ($request->filled('tryout_type')) {
            $query->whereHas('package', fn ($packageQuery) => $packageQuery
                ->where('tryout_type', $request->input('tryout_type')));
        }

        if ($request->filled('package_id')) {
            $query->where('tryout_package_id', (int) $request->input('package_id'));
        }

        return $query;
    }
}
