@extends('layouts.admin')

@php
    $title = 'Detail Sesi Tryout';
    $subtitle = ($tryoutSession->participant_name ?: 'Peserta Tanpa Nama') . ' - ' . ($tryoutSession->package?->title ?: 'Paket tidak ditemukan');
    $answeredCount = $tryoutSession->answers->whereNotNull('question_option_id')->count();
    $markedCount = $tryoutSession->answers->where('is_marked', true)->count();
    $totalQuestions = $tryoutSession->answers->count();
@endphp

@section('content')
<div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
    <div>
        <h5 class="mb-1">Ringkasan Peserta</h5>
        <p class="text-muted mb-0 fs-13">Pantau detail pengerjaan, skor per bagian, dan jawaban yang dipilih peserta.</p>
    </div>

    <a href="{{ route('admin.tryout-sessions.index') }}"
       class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1">
        <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
        <span>Kembali</span>
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-xl-3 col-md-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Status</p>
                <h4 class="mb-0 text-dark">{{ $statusLabel }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Hasil</p>
                <h4 class="mb-0 text-dark">{{ $resultLabel }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Skor Total</p>
                <h4 class="mb-0 text-dark">{{ number_format((int) $tryoutSession->total_score, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Progress Jawaban</p>
                <h4 class="mb-0 text-dark">{{ $answeredCount }}/{{ $totalQuestions }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card rc-dashboard-card h-100">
            <div class="card-body">
                <p class="text-muted fs-13 mb-1">Ditandai</p>
                <h4 class="mb-0 text-dark">{{ $markedCount }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Sesi</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Nama</dt>
                    <dd class="col-sm-7">{{ $tryoutSession->participant_name ?: '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Email</dt>
                    <dd class="col-sm-7 text-break">{{ $tryoutSession->participant_email ?: '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Paket</dt>
                    <dd class="col-sm-7">{{ $tryoutSession->package?->title ?: '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Jenis</dt>
                    <dd class="col-sm-7">{{ $tryoutSession->package?->tryout_type_label ?: '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Durasi</dt>
                    <dd class="col-sm-7">{{ (int) $tryoutSession->duration_minutes }} menit</dd>

                    <dt class="col-sm-5 text-muted">Dibuat</dt>
                    <dd class="col-sm-7">{{ $tryoutSession->created_at?->format('d M Y H:i') ?: '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Mulai</dt>
                    <dd class="col-sm-7">{{ $tryoutSession->started_at?->format('d M Y H:i') ?: '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Selesai</dt>
                    <dd class="col-sm-7">{{ $tryoutSession->finished_at?->format('d M Y H:i') ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Skor Per Bagian</h5>
            </div>
            <div class="card-body">
                @if ($sections->count())
                    <div class="table-responsive">
                        <table class="table table-centered align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Bagian</th>
                                    <th>Skor</th>
                                    <th>Terjawab</th>
                                    <th>Ditandai</th>
                                    <th>Kebutuhan Soal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sections as $section)
                                    <tr>
                                        <td class="fw-medium text-dark">{{ $section['label'] }}</td>
                                        <td>{{ number_format((int) $section['score'], 0, ',', '.') }}</td>
                                        <td>{{ $section['answered'] }}</td>
                                        <td>{{ $section['marked'] }}</td>
                                        <td>{{ $section['required'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Komposisi bagian tidak ditemukan pada paket tryout ini.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Daftar Jawaban</h5>
            </div>
            <div class="card-body">
                @if ($tryoutSession->answers->count())
                    <div class="table-responsive table-card">
                        <table class="table table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Soal</th>
                                    <th>Bagian</th>
                                    <th>Kategori</th>
                                    <th>Jawaban Dipilih</th>
                                    <th>Skor</th>
                                    <th>Flag</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tryoutSession->answers as $answer)
                                    <tr>
                                        <td>
                                            <div class="fw-medium text-dark">#{{ $loop->iteration }}</div>
                                            <div class="text-muted fs-13 text-wrap" style="max-width: 420px;">
                                                {{ \Illuminate\Support\Str::limit(strip_tags((string) $answer->question?->question_text), 120) }}
                                            </div>
                                        </td>
                                        <td>{{ $answer->question?->section_label ?? strtoupper((string) $answer->question?->section) }}</td>
                                        <td>{{ $answer->question?->category?->name ?? '-' }}</td>
                                        <td>
                                            @if ($answer->option)
                                                <span class="fw-medium text-dark">{{ $answer->option->option_label }}</span>
                                                <span class="text-muted">- {{ \Illuminate\Support\Str::limit(strip_tags((string) $answer->option->option_text), 60) }}</span>
                                            @else
                                                <span class="text-muted">Belum dijawab</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format((int) $answer->score, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($answer->is_marked)
                                                <span class="badge bg-warning-subtle text-warning rounded-pill">Ditandai</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Belum ada jawaban yang tersimpan untuk sesi ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
