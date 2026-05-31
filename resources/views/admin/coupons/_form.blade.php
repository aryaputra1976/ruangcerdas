@php
    $isEdit = isset($coupon);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Code <span class="text-danger">*</span></label>
        <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" name="name" value="{{ old('name', $coupon->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tipe <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="fixed" @selected(old('type', $coupon->type ?? 'fixed') === 'fixed')>Fixed</option>
            <option value="percent" @selected(old('type', $coupon->type ?? '') === 'percent')>Percent</option>
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Value <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value ?? 0) }}"
               class="form-control @error('value') is-invalid @enderror" required>
        @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Max Discount</label>
        <input type="number" step="0.01" min="0" name="max_discount" value="{{ old('max_discount', $coupon->max_discount ?? '') }}"
               class="form-control @error('max_discount') is-invalid @enderror">
        @error('max_discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Min Order Amount</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}"
               class="form-control @error('min_order_amount') is-invalid @enderror">
        @error('min_order_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Usage Limit</label>
        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
               class="form-control @error('usage_limit') is-invalid @enderror">
        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Is Active</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Starts At</label>
        <input type="datetime-local" name="starts_at"
               value="{{ old('starts_at', isset($coupon->starts_at) ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('starts_at') is-invalid @enderror">
        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Expires At</label>
        <input type="datetime-local" name="expires_at"
               value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('expires_at') is-invalid @enderror">
        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kupon' }}</button>
    <a href="{{ route('admin.coupons.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill px-4">Batal</a>
</div>
