@extends('layouts.admin')

@php
    $title = 'Laporan Tryout';
    $subtitle = 'Pantau ranking peserta dan performa paket tryout dari sesi yang sudah selesai.';
    $hasFilter = request()->filled('q') || request()->filled('tryout_type') || request()->filled('package_id') || request()->filled('from') || request()->filled('to');
    $summaryCards = [
        ['label' => 'Sesi Selesai', 'value' => $sessionsCount, 'icon' => 'check-circle', 'class' => 'success'],
        ['label' => 'Peserta Tercatat', 'value' => $participantsCount, 'icon' => 'users', 'class' => 'primary'],
        ['label' => 'Rata-rata Skor', 'value' => number_format((float) $averageScore, 1, ',', '.'), 'icon' => 'bar-chart-2', 'class' => 'info'],
        ['label' => 'Pass Rate', 'value' => number_format((float) $passRate, 1, ',', '.') . '%', 'icon' => 'award', 'class' => 'warning'],
    ];
@endphp

@section('content')
<div class="row g-3 mb-3">
    @foreach ($summaryCards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="card rc-dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 border border-{{ $card['class'] }} border-opacity-10 bg-{{ $card['class'] }}-subtle rounded-3">
                            <div class="bg-{{ $card['class'] }} rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i data-feather="{{ $card['icon'] }}" class="text-white" style="width: 17px; height: 17px;"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted fs-13 mb-1">{{ $card['label'] }}</p>
                            <h4 class="mb-0 text-dark">{{ $card['value'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Laporan Hasil Tryout</h5>
                <p class="text-muted mb-0 fs-13">
                    @if ($selectedPackage)
                        Fokus pada paket {{ $selectedPackage->title }} dan sesi yang sudah selesai.
                    @else
                        Ringkas hasil tryout per peserta dan per paket untuk kebutuhan monitoring admin.
                    @endif
                </p>
            </div>

            @if ($hasFilter)
                <a href="{{ route('admin.tryout-reports.index') }}"
                   class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3 d-inline-flex align-items-center gap-1">
                    <i data-feather="x" style="width: 14px; height: 14px;"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.tryout-reports.index') }}" class="row g-2">
            <div class="col-lg-4">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Cari peserta, email, atau paket...">
            </div>
            <div class="col-lg-3">
                <select name="tryout_type" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach ($tryoutTypes as $type => $label)
                        <option value="{{ $type }}" @selected(request('tryout_type') === $type)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
                <select name="package_id" class="form-select">
                    <option value="">Semua Paket</option>
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}" @selected((string) request('package_id') === (string) $package->id)>
                            {{ $package->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-lg-1">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <h5 class="card-title mb-1">Top Peserta</h5>
                        <p class="text-muted mb-0 fs-13">10 skor tertinggi dari sesi tryout yang sudah selesai.</p>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $topParticipants->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                @if ($topParticipants->isNotEmpty())
                    <div class="table-responsive table-card">
                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Peserta</th>
                                    <th>Paket</th>
                                    <th>Status</th>
                                    <th>Skor</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topParticipants as $session)
                                    @php($isPassed = $session->isPassed())
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $session->participant_name ?: 'Peserta Tanpa Nama' }}</div>
                                            <div class="text-muted fs-13">{{ $session->participant_email ?: '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-medium text-dark">{{ $session->package?->title ?? '-' }}</div>
                                            <div class="text-muted fs-13">{{ $session->package?->tryout_type_label ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{
                                                $isPassed === true
                                                    ? 'bg-success-subtle text-success'
                                                    : ($isPassed === false ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary')
                                            }} rounded-pill">
                                                {{ $isPassed === true ? 'Lulus' : ($isPassed === false ? 'Belum Lulus' : 'Tanpa Ambang Batas') }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">
                                            {{ number_format((int) $session->total_score, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.tryout-sessions.show', $session) }}"
                                               class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                                <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                <span>Detail</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-2">
                            <i data-feather="award" class="text-muted" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h6 class="text-muted mb-0">Belum ada sesi selesai untuk ditampilkan di leaderboard.</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <div>
                    <h5 class="card-title mb-1">Performa Paket</h5>
                    <p class="text-muted mb-0 fs-13">Urutan paket berdasarkan jumlah sesi dan kualitas hasil peserta.</p>
                </div>
            </div>
            <div class="card-body">
                @if ($packageReports->isNotEmpty())
                    <div class="list-group list-group-flush list-group-no-gutters">
                        @foreach ($packageReports as $report)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $report->package->title }}</div>
                                        <div class="text-muted fs-13">{{ $report->package->tryout_type_label }}</div>
                                        <div class="text-muted fs-13 mt-1">
                                            {{ number_format((int) $report->sessions_count, 0, ',', '.') }} sesi
                                            · {{ number_format((int) $report->participants_count, 0, ',', '.') }} peserta
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold text-dark">Avg {{ number_format((float) $report->average_score, 1, ',', '.') }}</div>
                                        <div class="text-muted fs-13">Top {{ number_format((int) $report->highest_score, 0, ',', '.') }}</div>
                                        <div class="text-muted fs-13">Pass {{ number_format((float) $report->pass_rate, 1, ',', '.') }}%</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.tryout-reports.index', array_merge(request()->except('page'), ['package_id' => $report->package->id])) }}"
                                       class="fs-13 text-primary fw-semibold">
                                        Lihat ranking paket ini
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-2">
                            <i data-feather="bar-chart-2" class="text-muted" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h6 class="text-muted mb-0">Belum ada paket dengan data sesi selesai.</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
