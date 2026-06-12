@extends('layouts.admin')

@php
    $title = 'Sesi Tryout';
    $subtitle = 'Pantau peserta, progres pengerjaan, dan hasil tryout dari admin.';
    $hasFilter = request()->filled('q') || request()->filled('status') || request()->filled('tryout_type') || request()->filled('package_id') || request()->filled('result') || request()->filled('from') || request()->filled('to');
    $statusCards = [
        ['key' => null, 'label' => 'Semua Sesi', 'count' => $counts['all'] ?? 0, 'icon' => 'layers', 'class' => 'primary'],
        ['key' => 'draft', 'label' => 'Draft', 'count' => $counts['draft'] ?? 0, 'icon' => 'file-text', 'class' => 'secondary'],
        ['key' => 'ongoing', 'label' => 'Berjalan', 'count' => $counts['ongoing'] ?? 0, 'icon' => 'clock', 'class' => 'warning'],
        ['key' => 'finished', 'label' => 'Selesai', 'count' => $counts['finished'] ?? 0, 'icon' => 'check-circle', 'class' => 'success'],
    ];

    $statusLabels = [
        'draft' => 'Draft',
        'ongoing' => 'Sedang Berjalan',
        'finished' => 'Selesai',
    ];
@endphp

@section('content')
<div class="row g-3 mb-3">
    @foreach ($statusCards as $card)
        @php
            $isActive = blank($card['key']) ? blank(request('status')) : request('status') === $card['key'];
            $url = blank($card['key'])
                ? route('admin.tryout-sessions.index', request()->except('status', 'page'))
                : route('admin.tryout-sessions.index', array_merge(request()->except('page'), ['status' => $card['key']]));
        @endphp
        <div class="col-xl-3 col-md-6">
            <a href="{{ $url }}" class="text-decoration-none">
                <div class="card rc-dashboard-card h-100 {{ $isActive ? 'border border-' . $card['class'] : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 border border-{{ $card['class'] }} border-opacity-10 bg-{{ $card['class'] }}-subtle rounded-3">
                                <div class="bg-{{ $card['class'] }} rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i data-feather="{{ $card['icon'] }}" class="text-white" style="width: 17px; height: 17px;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-muted fs-13 mb-1">{{ $card['label'] }}</p>
                                <h4 class="mb-0 text-dark">{{ number_format($card['count'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h5 class="card-title mb-1">Monitoring Sesi Tryout</h5>
                <p class="text-muted mb-0 fs-13">Lihat peserta, paket yang dikerjakan, progres jawaban, dan skor hasil tryout.</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.tryout-sessions.export', request()->except('page')) }}"
                   class="btn btn-sm bg-success-subtle text-success rounded-pill px-3 d-inline-flex align-items-center gap-1">
                    <i data-feather="download" style="width: 14px; height: 14px;"></i>
                    <span>Export CSV</span>
                </a>

                @if ($hasFilter)
                    <a href="{{ route('admin.tryout-sessions.index') }}"
                       class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.tryout-sessions.index') }}" class="row g-2 mb-4">
            <div class="col-lg-4">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Cari nama peserta, email, judul paket...">
            </div>
            <div class="col-lg-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach ($statusLabels as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
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
            <div class="col-lg-2">
                <select name="result" class="form-select">
                    <option value="">Semua Hasil</option>
                    <option value="passed" @selected(request('result') === 'passed')>Lulus</option>
                    <option value="failed" @selected(request('result') === 'failed')>Belum Lulus</option>
                </select>
            </div>
            <div class="col-lg-2">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-lg-2">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-lg-12 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Filter</button>
            </div>
        </form>

        @if ($sessions->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Peserta</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Hasil</th>
                            <th>Progress</th>
                            <th>Skor</th>
                            <th>Waktu</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            @php
                                $statusClass = match ($session->status) {
                                    'finished' => 'success',
                                    'ongoing' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $session->participant_name ?: 'Peserta Tanpa Nama' }}</div>
                                    <div class="text-muted fs-13">{{ $session->participant_email ?: '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $session->package?->title ?? 'Paket tidak ditemukan' }}</div>
                                    <div class="text-muted fs-13">
                                        {{ $session->package?->tryout_type_label ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} rounded-pill">
                                        {{ $statusLabels[$session->status] ?? ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td>
                                    @php($isPassed = $session->isPassed())
                                    <span class="badge {{
                                        $isPassed === true
                                            ? 'bg-success-subtle text-success'
                                            : ($isPassed === false ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary')
                                    }} rounded-pill">
                                        {{ $isPassed === true ? 'Lulus' : ($isPassed === false ? 'Belum Lulus' : ($session->status === 'finished' ? 'Tanpa Ambang Batas' : '-')) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $session->answered_count }}/{{ $session->answers_count }}</div>
                                    <div class="text-muted fs-13">Terjawab / total soal</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ number_format((int) $session->total_score, 0, ',', '.') }}</div>
                                    <div class="text-muted fs-13">
                                        TWK {{ (int) $session->twk_score }} · TIU {{ (int) $session->tiu_score }} · TKP {{ (int) $session->tkp_score }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted">{{ $session->created_at?->format('d M Y H:i') }}</div>
                                    @if ($session->finished_at)
                                        <div class="text-muted fs-13">Selesai {{ $session->finished_at->format('d M Y H:i') }}</div>
                                    @elseif ($session->started_at)
                                        <div class="text-muted fs-13">Mulai {{ $session->started_at->format('d M Y H:i') }}</div>
                                    @endif
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

            <div class="mt-3">
                {{ $sessions->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i data-feather="inbox" class="text-muted" style="width: 46px; height: 46px;"></i>
                </div>
                <h5 class="text-dark mb-1">{{ $hasFilter ? 'Sesi tryout tidak ditemukan' : 'Belum ada sesi tryout' }}</h5>
                <p class="text-muted mb-0">
                    {{ $hasFilter ? 'Tidak ada sesi yang cocok dengan filter yang dipilih.' : 'Sesi peserta tryout akan muncul di halaman ini.' }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
