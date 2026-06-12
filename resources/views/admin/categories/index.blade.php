@extends('layouts.admin')

@php
    $title = 'Kategori Produk';
    $subtitle = 'Kelola kategori produk digital Ruang Cerdas.';

    $hasFilter = request()->filled('q') || request()->filled('status');

    $statusCards = [
        [
            'key' => null,
            'label' => 'Semua Kategori',
            'count' => $counts['all'] ?? 0,
            'icon' => 'grid',
            'class' => 'primary',
        ],
        [
            'key' => 'active',
            'label' => 'Aktif',
            'count' => $counts['active'] ?? 0,
            'icon' => 'check-circle',
            'class' => 'success',
        ],
        [
            'key' => 'inactive',
            'label' => 'Nonaktif',
            'count' => $counts['inactive'] ?? 0,
            'icon' => 'slash',
            'class' => 'secondary',
        ],
    ];
@endphp

@section('content')

<div class="row g-3 mb-3">
    @foreach ($statusCards as $card)
        @php
            $isActive = blank($card['key'])
                ? blank(request('status'))
                : request('status') === $card['key'];

            $url = blank($card['key'])
                ? route('admin.categories.index', request()->except('status', 'page'))
                : route('admin.categories.index', array_merge(request()->except('page'), ['status' => $card['key']]));
        @endphp

        <div class="col-xl-4 col-md-4 col-sm-6">
            <a href="{{ $url }}" class="text-decoration-none">
                <div class="card rc-dashboard-card h-100 {{ $isActive ? 'border border-' . $card['class'] : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 border border-{{ $card['class'] }} border-opacity-10 bg-{{ $card['class'] }}-subtle rounded-3">
                                <div class="bg-{{ $card['class'] }} rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 36px; height: 36px;">
                                    <i data-feather="{{ $card['icon'] }}"
                                       class="text-white"
                                       style="width: 17px; height: 17px;"></i>
                                </div>
                            </div>

                            <div>
                                <p class="text-muted fs-13 mb-1">
                                    {{ $card['label'] }}
                                </p>

                                <h4 class="mb-0 text-dark">
                                    {{ number_format($card['count'], 0, ',', '.') }}
                                </h4>
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
                <h5 class="card-title mb-1">
                    Daftar Kategori
                </h5>

                <p class="text-muted mb-0 fs-13">
                    Kategori membantu pembeli menemukan produk digital dengan lebih cepat.
                </p>
            </div>

            <a href="{{ route('admin.categories.create') }}"
               class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 mb-4">
            <div class="col-lg-6 col-md-6">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Cari nama, slug, atau deskripsi kategori...">
            </div>

            <div class="col-lg-3 col-md-6">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>
                        Aktif
                    </option>
                    <option value="inactive" @selected(request('status') === 'inactive')>
                        Nonaktif
                    </option>
                </select>
            </div>

            <div class="col-lg-auto col-md-12 d-flex gap-2 flex-wrap align-items-start">
                <button type="submit"
                        class="btn btn-primary rounded-pill px-3 d-flex d-sm-inline-flex align-items-center justify-content-center justify-content-sm-start gap-1 flex-shrink-0">
                    <i data-feather="filter" style="width: 14px; height: 14px;"></i>
                    <span>Filter</span>
                </button>

                @if ($hasFilter)
                    <a href="{{ route('admin.categories.index') }}"
                       class="btn bg-danger-subtle text-danger rounded-pill px-3 d-flex d-sm-inline-flex align-items-center justify-content-center justify-content-sm-start gap-1 flex-shrink-0">
                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @else
                    <a href="{{ route('admin.categories.index') }}"
                       class="btn bg-secondary-subtle text-secondary rounded-pill px-3 d-flex d-sm-inline-flex align-items-center justify-content-center justify-content-sm-start gap-1 flex-shrink-0">
                        <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i>
                        <span>Refresh</span>
                    </a>
                @endif
            </div>
        </form>

        @if ($hasFilter)
            <div class="alert alert-light border d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <div class="fs-13 text-muted">
                    Filter aktif:

                    @if (request('q'))
                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                            Keyword: {{ request('q') }}
                        </span>
                    @endif

                    @if (request('status'))
                        <span class="badge bg-info-subtle text-info rounded-pill">
                            Status: {{ request('status') === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    @endif
                </div>

                <a href="{{ route('admin.categories.index') }}"
                   class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3">
                    Hapus Filter
                </a>
            </div>
        @endif

        @if ($categories->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>Kategori</th>
                            <th style="width: 160px;">Jumlah Produk</th>
                            <th style="width: 120px;">Urutan</th>
                            <th style="width: 130px;">Status</th>
                            <th style="width: 150px;">Dibuat</th>
                            <th style="width: 170px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                                 style="width: 46px; height: 46px;">
                                                <i data-feather="folder" style="width: 21px; height: 21px;"></i>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $category->name }}
                                            </div>

                                            <div class="text-muted fs-13">
                                                {{ $category->slug }}
                                            </div>

                                            @if ($category->description)
                                                <div class="text-muted fs-13 text-wrap mt-1" style="max-width: 420px;">
                                                    {{ $category->description }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-info-subtle text-info fw-semibold rounded-pill">
                                        {{ number_format((int) ($category->products_count ?? 0), 0, ',', '.') }} Produk
                                    </span>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ number_format((int) ($category->sort_order ?? 0), 0, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    @if ($category->is_active)
                                        <span class="badge bg-success-subtle text-success fw-semibold rounded-pill">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold rounded-pill">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $category->created_at?->format('d M Y') ?? '-' }}
                                    </span>

                                    <div class="text-muted fs-13">
                                        {{ $category->created_at?->format('H:i') ?? '' }}
                                    </div>
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                            <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                            <span>Edit</span>
                                        </a>

                                        @if (($category->products_count ?? 0) == 0)
                                            <form method="POST"
                                                  action="{{ route('admin.categories.destroy', $category) }}"
                                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links('vendor.pagination.ruangcerdas') }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i data-feather="folder" class="text-muted" style="width: 46px; height: 46px;"></i>
                </div>

                <h5 class="text-dark mb-1">
                    @if ($hasFilter)
                        Kategori tidak ditemukan
                    @else
                        Belum ada kategori
                    @endif
                </h5>

                <p class="text-muted mb-3">
                    @if ($hasFilter)
                        Tidak ada kategori yang sesuai dengan filter pencarian.
                    @else
                        Kategori produk digital akan muncul di halaman ini.
                    @endif
                </p>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    @if ($hasFilter)
                        <a href="{{ route('admin.categories.index') }}"
                           class="btn bg-secondary-subtle text-secondary rounded-pill px-4">
                            Reset Filter
                        </a>
                    @endif

                    <a href="{{ route('admin.categories.create') }}"
                       class="btn btn-primary rounded-pill px-4">
                        Tambah Kategori
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
