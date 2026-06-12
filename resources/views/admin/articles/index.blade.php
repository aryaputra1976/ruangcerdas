@extends('layouts.admin')

@php
    $title = 'Artikel';
    $subtitle = 'Kelola artikel edukasi untuk SEO.';
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-1">Daftar Artikel</h5>
            <p class="text-muted mb-0 fs-13">Hanya artikel published yang tampil di halaman public.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Tambah Artikel</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul/slug/excerpt...">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-primary rounded-pill px-3" type="submit">Filter</button>
            </div>
        </form>

        @if ($articles->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Published At</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $article->title }}</div>
                                    <div class="text-muted fs-13">{{ $article->slug }}</div>
                                </td>
                                <td>
                                    @if ($article->is_published)
                                        <span class="badge bg-success-subtle text-success rounded-pill">Published</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $article->published_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Edit</a>
                                        <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?');">
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
            <div class="mt-3">{{ $articles->links('vendor.pagination.ruangcerdas') }}</div>
        @else
            <p class="text-muted mb-0">Belum ada artikel.</p>
        @endif
    </div>
</div>
@endsection
