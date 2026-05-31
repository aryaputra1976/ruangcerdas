@extends('layouts.admin')

@php
    $title = 'Pengaturan Pembayaran';
    $subtitle = 'Atur informasi transfer dan QRIS untuk checkout manual.';
@endphp

@section('content')

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-1">Form Pengaturan Pembayaran</h5>
                <p class="text-muted mb-0 fs-13">
                    Ubah informasi bank, catatan pembayaran, status aktif, dan file QRIS.
                </p>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.payment-settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label">Nama Bank</label>
                            <input type="text"
                                   id="bank_name"
                                   name="bank_name"
                                   class="form-control @error('bank_name') is-invalid @enderror"
                                   value="{{ old('bank_name', $paymentSetting->bank_name) }}"
                                   placeholder="Contoh: Bank Mandiri">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="bank_account_number" class="form-label">Nomor Rekening</label>
                            <input type="text"
                                   id="bank_account_number"
                                   name="bank_account_number"
                                   class="form-control @error('bank_account_number') is-invalid @enderror"
                                   value="{{ old('bank_account_number', $paymentSetting->bank_account_number) }}"
                                   placeholder="Contoh: 1234567890">
                            @error('bank_account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="bank_account_holder" class="form-label">Nama Pemilik Rekening</label>
                            <input type="text"
                                   id="bank_account_holder"
                                   name="bank_account_holder"
                                   class="form-control @error('bank_account_holder') is-invalid @enderror"
                                   value="{{ old('bank_account_holder', $paymentSetting->bank_account_holder) }}"
                                   placeholder="Contoh: Ruang Cerdas">
                            @error('bank_account_holder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="payment_note" class="form-label">Catatan Pembayaran</label>
                            <textarea id="payment_note"
                                      name="payment_note"
                                      rows="4"
                                      class="form-control @error('payment_note') is-invalid @enderror"
                                      placeholder="Catatan akan ditampilkan ke pembeli.">{{ old('payment_note', $paymentSetting->payment_note) }}</textarea>
                            @error('payment_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="qris_image" class="form-label">Upload QRIS</label>
                            <input type="file"
                                   id="qris_image"
                                   name="qris_image"
                                   accept="image/*"
                                   class="form-control @error('qris_image') is-invalid @enderror">
                            @error('qris_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">File akan disimpan ke disk public: <code>payment/qris</code>.</div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="remove_qris"
                                       name="remove_qris"
                                       value="1"
                                       {{ old('remove_qris') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remove_qris">
                                    Hapus QRIS saat ini
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $paymentSetting->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Pengaturan pembayaran aktif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-1">
                            <i data-feather="save" style="width: 14px; height: 14px;"></i>
                            <span>Simpan Pengaturan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Preview QRIS</h5>
            </div>
            <div class="card-body">
                @if ($paymentSetting->qris_image_path)
                    <img src="{{ Storage::disk('public')->url($paymentSetting->qris_image_path) }}"
                         alt="QRIS"
                         class="img-fluid rounded border mb-3">
                    <p class="text-muted fs-13 mb-0">
                        Path: <code>{{ $paymentSetting->qris_image_path }}</code>
                    </p>
                @else
                    <div class="alert alert-light border mb-0">
                        QRIS belum diupload.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
