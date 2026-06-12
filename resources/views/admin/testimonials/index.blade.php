@extends('layouts.admin')

@php
    $title = 'Testimonial';
    $subtitle = 'Kelola social proof dari pembeli.';
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-1">Daftar Testimonial</h5>
            <p class="text-muted mb-0 fs-13">Testimonial aktif akan ditampilkan di halaman public.</p>
        </div>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Tambah Testimonial</a>
    </div>
    <div class="card-body">
        @if ($testimonials->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Nama</th>
                            <th>Konten</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Urutan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($testimonials as $testimonial)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $testimonial->name }}</div>
                                    <div class="text-muted fs-13">{{ $testimonial->role ?: '-' }}</div>
                                </td>
                                <td class="text-wrap" style="max-width: 420px;">{{ $testimonial->content }}</td>
                                <td>{{ str_repeat('★', (int) $testimonial->rating) }}</td>
                                <td>
                                    @if ($testimonial->is_active)
                                        <span class="badge bg-success-subtle text-success rounded-pill">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">Nonaktif</span>
                                    @endif
                                    @if ($testimonial->is_featured)
                                        <span class="badge bg-warning-subtle text-warning rounded-pill">Featured</span>
                                    @endif
                                </td>
                                <td>{{ $testimonial->sort_order }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Edit</a>
                                        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" onsubmit="return confirm('Hapus testimonial ini?');">
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
            <div class="mt-3">{{ $testimonials->links('vendor.pagination.ruangcerdas') }}</div>
        @else
            <p class="text-muted mb-0">Belum ada testimonial.</p>
        @endif
    </div>
</div>
@endsection
