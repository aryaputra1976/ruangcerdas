@extends('layouts.admin')

@php
    $title = 'Kategori Soal';
    $subtitle = 'Kelola kategori soal untuk bank soal tryout.';
    $hasFilter = request()->filled('q') || request()->filled('status') || request()->filled('section') || request()->filled('tryout_type') || request()->filled('position_target');
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Daftar Kategori Soal</h5>
            <p class="text-muted mb-0 fs-13">Kelompokkan bank soal berdasarkan jenis tryout dan section.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.question-categories.import') }}" class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">Import Kategori</a>
            <a href="{{ route('admin.question-categories.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Tambah Kategori</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.question-categories.index') }}" class="row g-2 mb-4">
            <div class="col-lg-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama atau slug kategori..."></div>
            <div class="col-lg-3">
                <select name="tryout_type" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach ($tryoutTypes as $type => $label)
                        <option value="{{ $type }}" @selected(request('tryout_type') === $type)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
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
            <div class="col-lg-3">
                <select name="position_target" class="form-select">
                    <option value="">Semua Jabatan</option>
                    @foreach ($positionsByType as $type => $positions)
                        @foreach ($positions as $positionKey => $positionLabel)
                            <option value="{{ $positionKey }}" @selected(request('position_target') === $positionKey)>{{ $positionLabel }}</option>
                        @endforeach
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
            <div class="col-lg-1"><button type="submit" class="btn btn-primary rounded-pill w-100">Filter</button></div>
        </form>

        @if ($categories->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Section</th>
                            <th>Jabatan</th>
                            <th>Jumlah Soal</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $category->name }}</div>
                                    <div class="text-muted fs-13">{{ $category->slug }}</div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary rounded-pill">{{ $tryoutTypes[$category->tryout_type] ?? $category->tryout_type }}</span></td>
                                <td><span class="badge bg-info-subtle text-info rounded-pill">{{ $category->section_label }}</span></td>
                                <td>{{ $category->position_target_label ?? '-' }}</td>
                                <td>{{ $category->questions_count }}</td>
                                <td>
                                    <span class="badge {{ $category->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                        <a href="{{ route('admin.question-categories.edit', $category) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Edit</a>
                                        @if ($category->questions_count == 0)
                                            <form method="POST" action="{{ route('admin.question-categories.destroy', $category) }}" onsubmit="return confirm('Yakin ingin menghapus kategori soal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $categories->links() }}</div>
        @else
            <div class="text-center py-5">
                <h5 class="text-dark mb-1">{{ $hasFilter ? 'Kategori soal tidak ditemukan' : 'Belum ada kategori soal' }}</h5>
                <p class="text-muted mb-3">Buat kategori soal untuk memudahkan pengelolaan bank soal.</p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('admin.question-categories.import') }}" class="btn bg-info-subtle text-info rounded-pill px-4">Import Kategori</a>
                    <a href="{{ route('admin.question-categories.create') }}" class="btn btn-primary rounded-pill px-4">Tambah Kategori</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
