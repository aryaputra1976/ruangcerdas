@php
    $isEdit = isset($product);
@endphp

<div class="row">
    <div class="col-xl-8">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Informasi Produk
                </h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-8">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $product->name ?? '') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Kantor Cerdas AI Kit"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Tanpa Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Slug</label>
                        <input type="text"
                               name="slug"
                               value="{{ old('slug', $product->slug ?? '') }}"
                               class="form-control @error('slug') is-invalid @enderror"
                               placeholder="Kosongkan untuk otomatis">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Kosongkan jika ingin dibuat otomatis dari nama produk.
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Deskripsi Singkat</label>
                        <input type="text"
                               name="short_description"
                               value="{{ old('short_description', $product->short_description ?? '') }}"
                               class="form-control @error('short_description') is-invalid @enderror"
                               placeholder="Deskripsi pendek untuk katalog produk">
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Deskripsi Lengkap</label>
                        <textarea name="description"
                                  rows="6"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Jelaskan isi dan manfaat produk">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Benefits</label>
                        <textarea name="benefits"
                                  rows="5"
                                  class="form-control @error('benefits') is-invalid @enderror"
                                  placeholder="Tulis manfaat produk, satu per baris">{{ old('benefits', $product->benefits ?? '') }}</textarea>
                        @error('benefits')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contents</label>
                        <textarea name="contents"
                                  rows="5"
                                  class="form-control @error('contents') is-invalid @enderror"
                                  placeholder="Tulis isi paket produk, satu per baris">{{ old('contents', $product->contents ?? '') }}</textarea>
                        @error('contents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Harga Produk
                </h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                        <input type="number"
                               name="normal_price"
                               value="{{ old('normal_price', $product->normal_price ?? 0) }}"
                               class="form-control @error('normal_price') is-invalid @enderror"
                               min="0"
                               required>
                        @error('normal_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Harga Promo</label>
                        <input type="number"
                               name="sale_price"
                               value="{{ old('sale_price', $product->sale_price ?? '') }}"
                               class="form-control @error('sale_price') is-invalid @enderror"
                               min="0">
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Harga Pembeli Pertama</label>
                        <input type="number"
                               name="first_buyer_price"
                               value="{{ old('first_buyer_price', $product->first_buyer_price ?? '') }}"
                               class="form-control @error('first_buyer_price') is-invalid @enderror"
                               min="0">
                        @error('first_buyer_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kuota Pembeli Pertama</label>
                        <input type="number"
                               name="first_buyer_quota"
                               value="{{ old('first_buyer_quota', $product->first_buyer_quota ?? '') }}"
                               class="form-control @error('first_buyer_quota') is-invalid @enderror"
                               min="0">
                        @error('first_buyer_quota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="col-xl-4">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Status Produk
                </h5>
            </div>

            <div class="card-body">

                <div class="form-check form-switch mb-3">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-check-input"
                           id="is_active"
                           @checked(old('is_active', $product->is_active ?? true))>
                    <label class="form-check-label" for="is_active">
                        Produk Aktif
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox"
                           name="is_featured"
                           value="1"
                           class="form-check-input"
                           id="is_featured"
                           @checked(old('is_featured', $product->is_featured ?? false))>
                    <label class="form-check-label" for="is_featured">
                        Produk Unggulan
                    </label>
                </div>

                <div class="form-check form-switch">
                    <input type="checkbox"
                           name="is_published"
                           value="1"
                           class="form-check-input"
                           id="is_published"
                           @checked(old('is_published', !empty($product->published_at ?? null)))>
                    <label class="form-check-label" for="is_published">
                        Publish di Website
                    </label>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Cover Produk
                </h5>
            </div>

            <div class="card-body">

                @if (!empty($product->cover_image))
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $product->cover_image) }}"
                             alt="{{ $product->name }}"
                             class="img-fluid rounded-3 border">
                    </div>
                @endif

                <input type="file"
                       name="cover_image"
                       accept="image/*"
                       class="form-control @error('cover_image') is-invalid @enderror">

                @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    Maksimal 2MB. Format gambar umum.
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    File ZIP Private
                </h5>
            </div>

            <div class="card-body">

                @if (!empty($product->digital_file_path))
                    <div class="alert alert-success">
                        <div class="fw-semibold mb-1">File tersedia</div>
                        <div class="fs-13">
                            {{ $product->download_filename ?? $product->digital_file_path }}
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        File ZIP produk belum diupload.
                    </div>
                @endif

                <input type="file"
                       name="digital_file"
                       accept=".zip,application/zip,application/x-zip-compressed"
                       class="form-control @error('digital_file') is-invalid @enderror">

                @error('digital_file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    File akan disimpan di storage private, bukan folder public.
                </div>

            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary rounded-pill">
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
            </button>

            <a href="{{ route('admin.products.index') }}"
               class="btn bg-secondary-subtle text-secondary rounded-pill">
                Batal
            </a>
        </div>

    </div>
</div>