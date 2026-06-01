@php
    $isEdit = isset($user);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama</label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name ?? '') }}"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email"
               id="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email ?? '') }}"
               required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">
            Password {{ $isEdit ? '(Opsional)' : '' }}
        </label>
        <input type="password"
               id="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               {{ $isEdit ? '' : 'required' }}>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">
            Konfirmasi Password {{ $isEdit ? '(Opsional)' : '' }}
        </label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               class="form-control">
    </div>

    <div class="col-md-6">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="admin" @selected(old('role', $user->role ?? 'customer') === 'admin')>Admin</option>
            <option value="customer" @selected(old('role', $user->role ?? 'customer') === 'customer')>Customer</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                User aktif
            </label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary rounded-pill px-4">
        {{ $isEdit ? 'Update User' : 'Simpan User' }}
    </button>
    <a href="{{ route('admin.users.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill px-4">
        Batal
    </a>
</div>

