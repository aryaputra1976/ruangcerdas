@extends('layouts.admin')

@php($title = 'Lead Magnet')
@php($subtitle = 'Kelola panduan gratis untuk mengumpulkan leads.')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Daftar Lead Magnet</h5>
        <a href="{{ route('admin.lead-magnets.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Tambah Lead Magnet</a>
    </div>
    <div class="card-body">
        @if ($leadMagnets->count())
            <div class="table-responsive table-card">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted"><tr><th>Judul</th><th>Status</th><th>Download</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                    @foreach ($leadMagnets as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->title }}</div>
                                <div class="text-muted fs-13">{{ $item->slug }}</div>
                            </td>
                            <td>{!! $item->is_active ? '<span class="badge bg-success-subtle text-success rounded-pill">Aktif</span>' : '<span class="badge bg-secondary-subtle text-secondary rounded-pill">Nonaktif</span>' !!}</td>
                            <td>{{ number_format((int) $item->download_count, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.lead-magnets.edit', $item) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Edit</a>
                                <form method="POST" action="{{ route('admin.lead-magnets.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus lead magnet ini?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $leadMagnets->links() }}</div>
        @else
            <p class="text-muted mb-0">Belum ada lead magnet.</p>
        @endif
    </div>
</div>
@endsection
