@extends('layouts.admin')

@php
    $title = 'Manajemen User';
    $subtitle = 'Kelola akun user untuk akses admin dan customer.';
@endphp

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Daftar User</h5>
            <p class="text-muted mb-0 fs-13">Create, edit, dan hapus user.</p>
        </div>

        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
            Tambah User
        </a>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       class="form-control"
                       placeholder="Cari nama atau email...">
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>

        @if ($users->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="fw-medium text-dark">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>{{ $user->last_login_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">
                                            Edit
                                        </a>
                                        <form method="POST"
                                              action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $users->links() }}</div>
        @else
            <div class="text-center py-5">
                <h5 class="text-dark mb-1">User belum tersedia</h5>
                <p class="text-muted mb-0">Silakan tambah user baru.</p>
            </div>
        @endif
    </div>
</div>

@endsection
