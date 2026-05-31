@php
    $isEdit = isset($category);

    $isActive = old('is_active', $category->is_active ?? true);
    $sortOrder = old('sort_order', $category->sort_order ?? 0);
@endphp

<div class="row">

    <div class="col-xl-8">

        <div class="row g-3">

            <div class="col-md-12">
                <label class="form-label">
                    Nama Kategori <span class="text-danger">*</span>
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $category->name ?? '') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Contoh: Template Kantor"
                       required
                       autofocus>

                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    Gunakan nama kategori yang mudah dipahami pembeli.
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label">
                    Slug
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        /kategori/
                    </span>

                    <input type="text"
                           name="slug"
                           value="{{ old('slug', $category->slug ?? '') }}"
                           class="form-control @error('slug') is-invalid @enderror"
                           placeholder="Kosongkan untuk otomatis">
                </div>

                @error('slug')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    Kosongkan jika ingin dibuat otomatis dari nama kategori.
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label">
                    Deskripsi
                </label>

                <textarea name="description"
                          rows="5"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Jelaskan kategori ini digunakan untuk produk apa">{{ old('description', $category->description ?? '') }}</textarea>

                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    Deskripsi membantu admin memahami fungsi kategori.
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">
                    Urutan
                </label>

                <input type="number"
                       name="sort_order"
                       value="{{ $sortOrder }}"
                       class="form-control @error('sort_order') is-invalid @enderror"
                       min="0">

                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    Angka kecil tampil lebih dulu.
                </div>
            </div>

        </div>

    </div>

    <div class="col-xl-4">

        <div class="card border">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Status
                </h5>
            </div>

            <div class="card-body">

                <div class="form-check form-switch mb-3">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-check-input"
                           id="is_active"
                           @checked($isActive)>

                    <label class="form-check-label fw-semibold" for="is_active">
                        Kategori Aktif
                    </label>

                    <div class="form-text">
                        Jika aktif, kategori dapat digunakan untuk produk.
                    </div>
                </div>

                <div class="alert alert-info mb-0">
                    <div class="fw-semibold mb-1">
                        Catatan
                    </div>

                    <div class="fs-13">
                        Kategori yang memiliki produk tidak bisa dihapus. Nonaktifkan jika hanya ingin menyembunyikan penggunaannya.
                    </div>
                </div>

            </div>
        </div>

        <div class="card border">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit"
                            class="btn btn-primary rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                        <i data-feather="save" style="width: 15px; height: 15px;"></i>
                        <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}</span>
                    </button>

                    <a href="{{ route('admin.categories.index') }}"
                       class="btn bg-secondary-subtle text-secondary rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                        <i data-feather="x" style="width: 15px; height: 15px;"></i>
                        <span>Batal</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>