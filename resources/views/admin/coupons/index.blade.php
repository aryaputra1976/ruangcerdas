@extends('layouts.admin')

@php
    $title = 'Kupon Diskon';
    $subtitle = 'Kelola kode diskon sederhana untuk checkout.';
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-1">Daftar Kupon</h5>
            <p class="text-muted mb-0 fs-13">Kupon aktif bisa dipakai pembeli saat checkout.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Tambah Kupon</a>
    </div>
    <div class="card-body">
        @if ($coupons->count())
            <div class="table-responsive table-card">
                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Code</th>
                            <th>Tipe</th>
                            <th>Value</th>
                            <th>Penggunaan</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $coupon->code }}</div>
                                    <div class="text-muted fs-13">{{ $coupon->name ?: '-' }}</div>
                                </td>
                                <td class="text-uppercase">{{ $coupon->type }}</td>
                                <td>
                                    @if ($coupon->type === 'percent')
                                        {{ rtrim(rtrim(number_format((float) $coupon->value, 2, '.', ''), '0'), '.') }}%
                                    @else
                                        {{ \App\Support\Money::format($coupon->value) }}
                                    @endif
                                </td>
                                <td>
                                    {{ $coupon->used_count }}
                                    @if (!is_null($coupon->usage_limit))
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </td>
                                <td class="fs-13">
                                    Mulai: {{ $coupon->starts_at?->format('d M Y H:i') ?? '-' }}<br>
                                    Exp: {{ $coupon->expires_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td>
                                    @if ($coupon->is_active)
                                        <span class="badge bg-success-subtle text-success rounded-pill">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3">Edit</a>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Hapus kupon ini?');">
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
            <div class="mt-3">{{ $coupons->links() }}</div>
        @else
            <p class="text-muted mb-0">Belum ada kupon.</p>
        @endif
    </div>
</div>
@endsection
