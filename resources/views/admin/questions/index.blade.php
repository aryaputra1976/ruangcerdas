@extends('layouts.admin')

@php
    $title = 'Bank Soal';
    $subtitle = 'Kelola bank soal tryout beserta opsi jawabannya.';
    $hasFilter = request()->filled('q') || request()->filled('section') || request()->filled('difficulty') || request()->filled('status') || request()->filled('question_category_id') || request()->filled('tryout_type');
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Daftar Soal</h5>
            <p class="text-muted mb-0 fs-13">Pastikan setiap soal memiliki 5 opsi A-E sesuai aturan scoring section.</p>
        </div>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Tambah Soal</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.questions.index') }}" class="row g-2 mb-4">
            <div class="col-lg-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari teks soal atau pembahasan..."></div>
            <div class="col-lg-2">
                <select name="tryout_type" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach ($tryoutTypes as $type => $label)
                        <option value="{{ $type }}" @selected(request('tryout_type') === $type)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <select name="section" class="form-select">
                    <option value="">Semua Section</option>
                    @foreach ($sectionsByType as $type => $sections)
                        <optgroup label="{{ $tryoutTypes[$type] }}">
                            @foreach ($sections as $sectionKey => $sectionLabel)
                                <option value="{{ $sectionKey }}" @selected(request('section') === $sectionKey)>{{ $sectionLabel }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <select name="difficulty" class="form-select">
                    <option value="">Semua Level</option>
                    @foreach (['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('difficulty') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
                <select name="question_category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('question_category_id') === (string) $category->id)>{{ $tryoutTypes[$category->tryout_type] ?? $category->tryout_type }} - {{ $category->section_label }} - {{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Filter</button>
                @if ($hasFilter)
                    <a href="{{ route('admin.questions.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill px-4">Reset</a>
                @endif
            </div>
        </form>

        @if ($questions->count())
            <div class="table-responsive table-card">
                <table class="table table-hover align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Soal</th>
                            <th>Jenis</th>
                            <th>Section</th>
                            <th>Kategori</th>
                            <th>Level</th>
                            <th>Opsi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $question)
                            <tr>
                                <td style="min-width: 320px;">
                                    <div class="fw-semibold text-dark">{{ \Illuminate\Support\Str::limit(strip_tags($question->question_text), 110) }}</div>
                                    @if ($question->explanation)
                                        <div class="text-muted fs-13 mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($question->explanation), 90) }}</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary rounded-pill">{{ $tryoutTypes[$question->tryout_type] ?? $question->tryout_type }}</span></td>
                                <td><span class="badge bg-info-subtle text-info rounded-pill">{{ $question->section_label }}</span></td>
                                <td>{{ $question->category?->name ?? '-' }}</td>
                                <td>{{ ucfirst($question->difficulty) }}</td>
                                <td>{{ $question->options->count() }} opsi</td>
                                <td>
                                    <span class="badge {{ $question->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill">
                                        {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                        <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Edit</a>
                                        <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $questions->links() }}</div>
        @else
            <div class="text-center py-5">
                <h5 class="text-dark mb-1">{{ $hasFilter ? 'Soal tidak ditemukan' : 'Belum ada bank soal' }}</h5>
                <p class="text-muted mb-3">Tambahkan soal untuk mulai membangun bank soal.</p>
                <a href="{{ route('admin.questions.create') }}" class="btn btn-primary rounded-pill px-4">Tambah Soal</a>
            </div>
        @endif
    </div>
</div>
@endsection
