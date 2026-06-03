@extends('layouts.admin')

@php
    $title = 'Kategori Soal';
    $subtitle = 'Kelola kategori soal untuk bank soal tryout CPNS.';
    $hasFilter = request()->filled('q') || request()->filled('status') || request()->filled('section');
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Daftar Kategori Soal</h5>
            <p class="text-muted mb-0 fs-13">Kelompokkan bank soal berdasarkan section CPNS.</p>
        </div>
        <a href="{{ route('admin.question-categories.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Tambah Kategori</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.question-categories.index') }}" class="row g-2 mb-4">
            <div class="col-lg-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama atau slug kategori..."></div>
            <div class="col-lg-3">
                <select name="section" class="form-select">
                    <option value="">Semua Section</option>
                    @foreach (['TWK', 'TIU', 'TKP'] as $section)
                        <option value="{{ $section }}" @selected(request('section') === $section)>{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="col-lg-2"><button type="submit" class="btn btn-primary rounded-pill w-100">Filter</button></div>
        </form>

        @if ($categories->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Kategori</th>
                            <th>Section</th>
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
                                <td><span class="badge bg-primary-subtle text-primary rounded-pill">{{ $category->section }}</span></td>
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
                <a href="{{ route('admin.question-categories.create') }}" class="btn btn-primary rounded-pill px-4">Tambah Kategori</a>
            </div>
        @endif
    </div>
</div>
@endsection
