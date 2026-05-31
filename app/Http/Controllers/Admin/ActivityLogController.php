<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'action' => trim((string) $request->query('action', '')),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'per_page' => (int) $request->integer('per_page', 20),
        ];

        $perPage = in_array($filters['per_page'], [20, 25], true) ? $filters['per_page'] : 20;

        $logs = ActivityLog::query()
            ->with('user:id,name,email')
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $q = $filters['q'];

                $query->where(function ($sub) use ($q) {
                    $sub->where('action', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('subject_type', 'like', "%{$q}%")
                        ->orWhere('ip_address', 'like', "%{$q}%");
                });
            })
            ->when($filters['action'] !== '', fn ($query) => $query->where('action', $filters['action']))
            ->when($filters['from'], fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $actions = ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'filters', 'actions'));
    }
}
