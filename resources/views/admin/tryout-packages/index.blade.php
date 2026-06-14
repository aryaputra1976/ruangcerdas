@extends('layouts.admin')

@php
    $title = 'Paket Tryout';
    $subtitle = 'Kelola paket tryout yang tampil di halaman public.';
    $hasFilter = request()->filled('q') || request()->filled('status') || request()->filled('tryout_type') || request()->filled('position_target');
    $statusCards = [
        ['key' => null, 'label' => 'Semua Paket', 'count' => $counts['all'] ?? 0, 'icon' => 'package', 'class' => 'primary'],
        ['key' => 'active', 'label' => 'Aktif', 'count' => $counts['active'] ?? 0, 'icon' => 'check-circle', 'class' => 'success'],
        ['key' => 'inactive', 'label' => 'Nonaktif', 'count' => $counts['inactive'] ?? 0, 'icon' => 'slash', 'class' => 'secondary'],
    ];
@endphp

@section('content')
<div class="row g-3 mb-3">
    @foreach ($statusCards as $card)
        @php
            $isActive = blank($card['key']) ? blank(request('status')) : request('status') === $card['key'];
            $url = blank($card['key'])
                ? route('admin.tryout-packages.index', request()->except('status', 'page'))
                : route('admin.tryout-packages.index', array_merge(request()->except('page'), ['status' => $card['key']]));
        @endphp
        <div class="col-xl-4 col-md-4 col-sm-6">
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
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Daftar Paket Tryout</h5>
            <p class="text-muted mb-0 fs-13">Atur paket, harga, durasi, dan komposisi soal tryout.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.tryout-packages.import') }}" class="btn btn-sm bg-success-subtle text-success rounded-pill px-3 d-inline-flex align-items-center gap-1">
                <i data-feather="upload" style="width: 14px; height: 14px;"></i>
                <span>Import Paket</span>
            </a>
            <a href="{{ route('admin.tryout-packages.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                <span>Tambah Paket</span>
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.tryout-packages.index') }}" class="row g-2 mb-4">
            <div class="col-lg-5">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul, slug, atau deskripsi paket...">
            </div>
            <div class="col-lg-3">
                <select name="tryout_type" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach ($tryoutTypes as $type => $label)
                        <option value="{{ $type }}" @selected(request('tryout_type') === $type)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
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
            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-3 w-100">Filter</button>
            </div>
        </form>

        @if ($packages->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Paket</th>
                            <th>Jenis</th>
                            <th>Jabatan</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Komposisi</th>
                            <th>Stok Soal</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $package->title }}</div>
                                    <div class="text-muted fs-13">{{ $package->slug }}</div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary rounded-pill">{{ $package->tryout_type_label }}</span></td>
                                <td>{{ $package->position_target_label ?? '-' }}</td>
                                <td>{{ $package->price > 0 ? 'Rp ' . number_format($package->price, 0, ',', '.') : 'Gratis' }}</td>
                                <td>{{ $package->duration_minutes }} menit</td>
                                <td class="fs-13 text-muted">{{ collect($package->sectionSummaries())->map(fn ($section) => $section['label'] . ' ' . $section['count'])->implode(' · ') }}</td>
                                <td style="min-width: 240px;">
                                    @php($stockStatus = $packageStockStatuses[$package->id] ?? ['enough' => true, 'sections' => []])
                                    <div class="mb-2">
                                        <span class="badge {{ $stockStatus['enough'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill">
                                            {{ $stockStatus['enough'] ? 'Stok Aman' : 'Stok Kurang' }}
                                        </span>
                                    </div>
                                    <div class="fs-13 text-muted">
                                        {{ collect($stockStatus['sections'])->map(fn ($section) => $section['label'] . ' ' . $section['available'] . '/' . $section['required'])->implode(' · ') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $package->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} fw-semibold rounded-pill">
                                        {{ $package->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                        <a href="{{ route('admin.tryout-packages.edit', $package) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Edit</a>
                                        <form method="POST" action="{{ route('admin.tryout-packages.destroy', $package) }}" onsubmit="return confirm('Yakin ingin menghapus paket tryout ini?');">
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
            <div class="mt-3">{{ $packages->links() }}</div>
        @else
            <div class="text-center py-5">
                <h5 class="text-dark mb-1">{{ $hasFilter ? 'Paket tidak ditemukan' : 'Belum ada paket tryout' }}</h5>
                <p class="text-muted mb-3">{{ $hasFilter ? 'Tidak ada paket yang cocok dengan filter pencarian.' : 'Paket tryout yang dibuat akan muncul di halaman ini.' }}</p>
                <a href="{{ route('admin.tryout-packages.create') }}" class="btn btn-primary rounded-pill px-4">Tambah Paket</a>
            </div>
        @endif
    </div>
</div>
@endsection
