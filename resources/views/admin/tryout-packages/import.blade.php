@extends('layouts.admin')

@php
    $title = 'Import Paket Tryout';
    $subtitle = 'Upload CSV atau Excel untuk membuat banyak paket tryout sekaligus.';
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Import Paket Tryout</h5>
            <p class="text-muted mb-0 fs-13">Siapkan komposisi soal per jenis tryout, lalu import massal tanpa input satu per satu.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.tryout-packages.import.template') }}" class="btn btn-sm bg-info-subtle text-info rounded-pill px-3">Template CSV</a>
            <a href="{{ route('admin.tryout-packages.import.template', ['format' => 'xlsx']) }}" class="btn btn-sm bg-success-subtle text-success rounded-pill px-3">Template Excel</a>
            <a href="{{ route('admin.tryout-packages.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-7">
                <form method="POST" action="{{ route('admin.tryout-packages.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="preview">

                    <div class="mb-3">
                        <label for="import_file" class="form-label">File Import <span class="text-danger">*</span></label>
                        <input type="file" name="import_file" id="import_file" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="form-control @error('import_file') is-invalid @enderror" required>
                        @error('import_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($errors->has('import_file') && count($errors->get('import_file')) > 1)
                            <div class="alert alert-danger mt-3 mb-0">
                                <div class="fw-semibold mb-2">Ada baris yang perlu diperbaiki:</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->get('import_file') as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="alert alert-info">
                        <div class="fw-semibold mb-1">Aturan import paket</div>
                        <div class="fs-13">Isi hanya kolom jumlah section yang sesuai dengan `tryout_type` paket.</div>
                        <div class="fs-13">Untuk PPPK Tendik, isi `position_target` agar paket mengambil soal dari jabatan yang benar.</div>
                        <div class="fs-13">Kolom section lain boleh dibiarkan kosong dan akan dianggap `0`.</div>
                        <div class="fs-13">Sistem akan menolak paket jika stok soal aktif belum cukup atau total soal masih `0`.</div>
                        <div class="fs-13">Preview akan dibuat dulu sebelum data benar-benar disimpan.</div>
                    </div>

                    <div class="d-grid d-sm-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Preview Import</button>
                        <a href="{{ route('admin.tryout-packages.import.template') }}" class="btn bg-light border rounded-pill px-4">Ambil Template CSV</a>
                        <a href="{{ route('admin.tryout-packages.import.template', ['format' => 'xlsx']) }}" class="btn bg-light border rounded-pill px-4">Ambil Template Excel</a>
                    </div>
                </form>
            </div>

            <div class="col-xl-5">
                <div class="card border mb-0">
                    <div class="card-header">
                        <h5 class="card-title mb-1">Header Wajib</h5>
                        <p class="text-muted fs-13 mb-0">Gunakan urutan template agar import lebih aman.</p>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($headers as $header)
                                <span class="badge bg-light text-dark border rounded-pill">{{ $header }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($preview)
            <div class="card border mt-4">
                <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h5 class="card-title mb-1">Preview Paket Tryout</h5>
                        <p class="text-muted fs-13 mb-0">{{ $preview['count'] }} paket siap diimpor dari file {{ strtoupper($preview['source']) }}.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.tryout-packages.import.store') }}">
                        @csrf
                        <input type="hidden" name="action" value="commit">
                        <input type="hidden" name="preview_token" value="{{ $preview['token'] }}">
                        <button type="submit" class="btn btn-success rounded-pill px-4" onclick="return confirm('Simpan semua paket dari preview ini?')">Simpan Paket</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        @foreach ($preview['type_summary'] as $summary)
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-semibold text-dark">{{ $summary['type_label'] }}</div>
                                    <div class="text-muted fs-13">{{ $summary['position_label'] }}</div>
                                    <div class="mt-2"><span class="badge bg-success-subtle text-success rounded-pill">{{ $summary['count'] }} paket</span></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="table-responsive table-card">
                        <table class="table table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Paket</th>
                                    <th>Jenis</th>
                                    <th>Jabatan</th>
                                    <th>Harga</th>
                                    <th>Durasi</th>
                                    <th>Komposisi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($preview['preview_rows'] as $row)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $row['title'] }}</div>
                                            <div class="text-muted fs-13">{{ $row['slug'] }}</div>
                                        </td>
                                        <td>{{ $row['tryout_type_label'] }}</td>
                                        <td>{{ $row['position_label'] }}</td>
                                        <td>{{ $row['price'] > 0 ? 'Rp ' . number_format($row['price'], 0, ',', '.') : 'Gratis' }}</td>
                                        <td>{{ $row['duration_minutes'] }} menit</td>
                                        <td class="fs-13 text-muted" style="min-width: 240px;">{{ $row['composition'] }}</td>
                                        <td>
                                            <span class="badge {{ $row['is_active'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill">
                                                {{ $row['is_active'] ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($preview['count'] > count($preview['preview_rows']))
                        <div class="alert alert-light border mt-3 mb-0">
                            Preview menampilkan {{ count($preview['preview_rows']) }} paket pertama dari total {{ $preview['count'] }} paket.
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card border mt-4 mb-0">
            <div class="card-header">
                <h5 class="card-title mb-1">Referensi Jenis dan Section</h5>
                <p class="text-muted fs-13 mb-0">Isi jumlah section sesuai jenis tryout yang dipilih.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($tryoutTypes as $type => $label)
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="fw-semibold text-dark mb-2">{{ $label }}</div>
                                <div class="fs-13 text-muted mb-2">Nilai `tryout_type`: <code>{{ $type }}</code></div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($sectionsByType[$type] ?? [] as $sectionKey => $sectionLabel)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $sectionKey }}_count - {{ $sectionLabel }}</span>
                                    @endforeach
                                </div>
                                @if (! empty($positionsByType[$type] ?? []))
                                    <div class="fs-13 text-muted mt-3 mb-2">Nilai `position_target`:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($positionsByType[$type] as $positionKey => $positionLabel)
                                            <span class="badge bg-warning-subtle text-warning rounded-pill">{{ $positionKey }} - {{ $positionLabel }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
