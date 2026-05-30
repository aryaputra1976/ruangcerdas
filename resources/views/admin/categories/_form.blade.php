@php
    $isEdit = isset($category);
@endphp

<div class="row">
    <div class="col-xl-8">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Informasi Kategori
                </h5>
            </div>

            <div class="card-body">
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
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Slug
                        </label>

                        <input type="text"
                               name="slug"
                               value="{{ old('slug', $category->slug ?? '') }}"
                               class="form-control @error('slug') is-invalid @enderror"
                               placeholder="Kosongkan untuk otomatis">

                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Kosongkan jika ingin slug dibuat otomatis dari nama kategori.
                            Contoh: <code>template-kantor</code>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="col-xl-4">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Panduan
                </h5>
            </div>

            <div class="card-body">
                <div class="alert alert-primary mb-0">
                    <div class="fw-semibold mb-1">
                        Tips kategori
                    </div>

                    <div class="fs-13">
                        Gunakan nama kategori yang pendek dan mudah dipahami pembeli,
                        misalnya <strong>Template Kantor</strong>, <strong>Aplikasi</strong>,
                        atau <strong>Panduan AI</strong>.
                    </div>
                </div>
            </div>
        </div>

        @if ($isEdit)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Statistik
                    </h5>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 border border-primary border-opacity-10 bg-primary-subtle rounded-2">
                            <div class="bg-primary rounded-circle widget-size text-center">
                                <i data-feather="package" class="text-white" style="width: 18px; height: 18px;"></i>
                            </div>
                        </div>

                        <div>
                            <p class="text-muted fs-13 mb-1">
                                Jumlah Produk
                            </p>
                            <h4 class="mb-0">
                                {{ $category->products()->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary rounded-pill">
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}
            </button>

            <a href="{{ route('admin.categories.index') }}"
               class="btn bg-secondary-subtle text-secondary rounded-pill">
                Batal
            </a>
        </div>

    </div>
</div>