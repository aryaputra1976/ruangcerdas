@extends('layouts.admin')

@php
    $title = 'Activity Log';
    $subtitle = 'Audit sederhana untuk aktivitas penting admin.';
@endphp

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-1">Filter Activity Log</h5>
        <p class="text-muted mb-0 fs-13">Cari berdasarkan kata kunci, action, dan rentang tanggal.</p>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3">
            <div class="col-xl-3 col-md-6">
                <label for="q" class="form-label">Kata Kunci</label>
                <input type="text" id="q" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="action, deskripsi, subject, IP">
            </div>

            <div class="col-xl-3 col-md-6">
                <label for="action" class="form-label">Action</label>
                <select id="action" name="action" class="form-select">
                    <option value="">Semua Action</option>
                    @foreach ($actionOptions as $action)
                        <option value="{{ $action }}" @selected($filters['action'] === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-2 col-md-6">
                <label for="from" class="form-label">Tanggal Mulai</label>
                <input type="date" id="from" name="from" value="{{ $filters['from'] }}" class="form-control">
            </div>

            <div class="col-xl-2 col-md-6">
                <label for="to" class="form-label">Tanggal Akhir</label>
                <input type="date" id="to" name="to" value="{{ $filters['to'] }}" class="form-control">
            </div>

            <div class="col-xl-2 col-md-6">
                <label for="per_page" class="form-label">Per Halaman</label>
                <select id="per_page" name="per_page" class="form-select">
                    <option value="20" @selected((int) $filters['per_page'] === 20)>20</option>
                    <option value="25" @selected((int) $filters['per_page'] === 25)>25</option>
                </select>
            </div>

            <div class="col-12 d-flex align-items-center gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="filter" style="width: 14px; height: 14px;"></i>
                    <span>Terapkan Filter</span>
                </button>

                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-light rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-1">Data Activity Log</h5>
        <p class="text-muted mb-0 fs-13">Menampilkan aktivitas admin terbaru.</p>
    </div>
    <div class="card-body">
        @if ($logs->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th style="width: 160px;">Waktu</th>
                            <th style="width: 220px;">Admin/User</th>
                            <th style="width: 180px;">Action</th>
                            <th style="width: 160px;">Subject</th>
                            <th>Description</th>
                            <th style="width: 140px;">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>
                                    <div>{{ $log->created_at?->format('d M Y') }}</div>
                                    <div class="fs-13 text-muted">{{ $log->created_at?->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    @if ($log->user)
                                        <div class="fw-medium text-dark">{{ $log->user->name }}</div>
                                        <div class="text-muted fs-13">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-muted">System / Tidak diketahui</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $log->action }}</span></td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $log->subject_type ?? '-' }}</div>
                                    <div class="fs-13 text-muted">#{{ $log->subject_id ?? '-' }}</div>
                                </td>
                                <td class="text-wrap">{{ $log->description ?? '-' }}</td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i data-feather="file-text" class="text-muted mb-2" style="width: 44px; height: 44px;"></i>
                <h5 class="text-dark mb-1">Belum ada aktivitas</h5>
                <p class="text-muted mb-0">Aktivitas admin akan tampil di sini.</p>
            </div>
        @endif
    </div>
</div>

@endsection
