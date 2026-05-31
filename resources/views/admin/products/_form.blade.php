@php
    $isEdit = isset($product);

    $normalPrice = old('normal_price', $product->normal_price ?? 0);
    $salePrice = old('sale_price', $product->sale_price ?? '');
    $firstBuyerPrice = old('first_buyer_price', $product->first_buyer_price ?? '');
    $firstBuyerQuota = old('first_buyer_quota', $product->first_buyer_quota ?? '');

    $isActive = old('is_active', $product->is_active ?? true);
    $isFeatured = old('is_featured', $product->is_featured ?? false);
    $isPublished = old(
        'is_published',
        !empty($product->published_at ?? null) && $product->published_at->lte(now())
    );
@endphp

<div class="row">

    <div class="col-xl-8">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h5 class="card-title mb-1">
                            Informasi Produk
                        </h5>

                        <p class="text-muted mb-0 fs-13">
                            Isi data utama produk yang akan tampil di halaman public.
                        </p>
                    </div>

                    @if ($isEdit && !empty($product->slug))
                        <a href="{{ route('products.show', $product->slug) }}"
                           target="_blank"
                           class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <i data-feather="external-link" style="width: 14px; height: 14px;"></i>
                            <span>Lihat Produk</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-8">
                        <label class="form-label">
                            Nama Produk <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ old('name', $product->name ?? '') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Kantor Cerdas AI Kit"
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Gunakan nama yang jelas, singkat, dan mudah dipahami pembeli.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Kategori
                        </label>

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

                        <div class="form-text">
                            Pilih kategori agar katalog lebih mudah difilter.
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Slug
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                /produk/
                            </span>

                            <input type="text"
                                   name="slug"
                                   value="{{ old('slug', $product->slug ?? '') }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="Kosongkan untuk otomatis">
                        </div>

                        @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Kosongkan jika ingin dibuat otomatis dari nama produk.
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Deskripsi Singkat
                        </label>

                        <input type="text"
                               name="short_description"
                               value="{{ old('short_description', $product->short_description ?? '') }}"
                               class="form-control @error('short_description') is-invalid @enderror"
                               placeholder="Deskripsi pendek untuk katalog produk">

                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Deskripsi singkat akan tampil di kartu produk dan ringkasan halaman.
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Deskripsi Lengkap
                        </label>

                        <textarea name="description"
                                  rows="7"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Jelaskan isi produk, siapa target penggunanya, dan masalah apa yang diselesaikan">{{ old('description', $product->description ?? '') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Gunakan penjelasan yang meyakinkan agar pembeli paham manfaat produk.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Benefits
                        </label>

                        <textarea name="benefits"
                                  rows="6"
                                  class="form-control @error('benefits') is-invalid @enderror"
                                  placeholder="Contoh:
Mempercepat pekerjaan administrasi
Template siap pakai
Cocok untuk ASN, admin kantor, dan pelaku usaha">{{ old('benefits', $product->benefits ?? '') }}</textarea>

                        @error('benefits')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Tulis manfaat produk, satu manfaat per baris.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Contents
                        </label>

                        <textarea name="contents"
                                  rows="6"
                                  class="form-control @error('contents') is-invalid @enderror"
                                  placeholder="Contoh:
File template Excel
Panduan PDF
File contoh
Bonus prompt AI">{{ old('contents', $product->contents ?? '') }}</textarea>

                        @error('contents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Tulis isi paket produk, satu item per baris.
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h5 class="card-title mb-1">
                        Harga Produk
                    </h5>

                    <p class="text-muted mb-0 fs-13">
                        Atur harga normal, harga promo, dan harga khusus pembeli pertama.
                    </p>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Harga Normal <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">Rp</span>

                            <input type="number"
                                   name="normal_price"
                                   value="{{ $normalPrice }}"
                                   class="form-control @error('normal_price') is-invalid @enderror"
                                   min="0"
                                   required>
                        </div>

                        @error('normal_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Harga dasar produk sebelum promo.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Harga Promo
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">Rp</span>

                            <input type="number"
                                   name="sale_price"
                                   value="{{ $salePrice }}"
                                   class="form-control @error('sale_price') is-invalid @enderror"
                                   min="0"
                                   placeholder="Kosongkan jika tidak promo">
                        </div>

                        @error('sale_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Jika diisi, harga promo akan diprioritaskan setelah kuota pembeli pertama habis.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Harga Pembeli Pertama
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">Rp</span>

                            <input type="number"
                                   name="first_buyer_price"
                                   value="{{ $firstBuyerPrice }}"
                                   class="form-control @error('first_buyer_price') is-invalid @enderror"
                                   min="0"
                                   placeholder="Contoh: 39000">
                        </div>

                        @error('first_buyer_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Harga khusus untuk pembeli awal, misalnya 10 pembeli pertama.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Kuota Pembeli Pertama
                        </label>

                        <input type="number"
                               name="first_buyer_quota"
                               value="{{ $firstBuyerQuota }}"
                               class="form-control @error('first_buyer_quota') is-invalid @enderror"
                               min="0"
                               placeholder="Contoh: 10">

                        @error('first_buyer_quota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Isi 0 atau kosong jika harga pembeli pertama tidak digunakan.
                        </div>
                    </div>

                </div>

                <div class="alert alert-info mt-4 mb-0">
                    <div class="fw-semibold mb-1">
                        Urutan harga yang dipakai sistem
                    </div>

                    <div class="fs-13">
                        Harga pembeli pertama dipakai selama kuota masih tersedia. Setelah itu sistem memakai harga promo jika ada. Jika tidak ada promo, sistem memakai harga normal.
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
                           @checked($isActive)>

                    <label class="form-check-label fw-semibold" for="is_active">
                        Produk Aktif
                    </label>

                    <div class="form-text">
                        Jika aktif, produk boleh tampil selama juga dipublish.
                    </div>
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox"
                           name="is_featured"
                           value="1"
                           class="form-check-input"
                           id="is_featured"
                           @checked($isFeatured)>

                    <label class="form-check-label fw-semibold" for="is_featured">
                        Produk Unggulan
                    </label>

                    <div class="form-text">
                        Produk unggulan dapat ditampilkan di halaman utama.
                    </div>
                </div>

                <div class="form-check form-switch">
                    <input type="checkbox"
                           name="is_published"
                           value="1"
                           class="form-check-input"
                           id="is_published"
                           @checked($isPublished)>

                    <label class="form-check-label fw-semibold" for="is_published">
                        Publish di Website
                    </label>

                    <div class="form-text">
                        Jika aktif, produk dapat muncul di katalog public.
                    </div>
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
                    <div class="mb-3 text-center">
                        <img src="{{ asset('storage/' . $product->cover_image) }}"
                             alt="{{ $product->name }}"
                             class="rounded-4 border"
                             style="width: 100%; max-width: 260px; height: 180px; object-fit: cover;">
                    </div>
                @else
                    <div class="mb-3 text-center">
                        <div class="mx-auto rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                             style="width: 100%; max-width: 260px; height: 180px;">
                            <i data-feather="image" style="width: 42px; height: 42px;"></i>
                        </div>
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
                    Maksimal 2MB. Disarankan rasio 16:9 atau 4:3.
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    File Digital Private
                </h5>
            </div>

            <div class="card-body">

                @if (!empty($product->digital_file_path))
                    <div class="alert alert-success">
                        <div class="fw-semibold mb-1">
                            File tersedia
                        </div>

                        <div class="fs-13 text-break">
                            {{ $product->download_filename ?? basename($product->digital_file_path) }}
                        </div>

                        <div class="fs-13 text-muted mt-1">
                            Ukuran: {{ $product->formatted_file_size ?? '-' }}
                        </div>

                        <div class="fs-13 text-muted mt-1">
                            Upload: {{ $product->file_uploaded_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <a href="{{ route('admin.products.file.download', $product) }}"
                           class="btn btn-sm bg-primary-subtle text-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                            <span>Download File</span>
                        </a>

                        <form method="POST"
                              action="{{ route('admin.products.file.destroy', $product) }}"
                              onsubmit="return confirm('Hapus file digital produk ini?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm bg-danger-subtle text-danger rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                <span>Hapus File</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <div class="fw-semibold mb-1">
                            File digital belum diupload
                        </div>

                        <div class="fs-13">
                            Produk belum bisa didownload setelah pembayaran disetujui sampai file tersedia.
                        </div>
                    </div>
                @endif

                <input type="file"
                       name="digital_file"
                       accept=".zip,.rar,.7z,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                       class="form-control @error('digital_file') is-invalid @enderror">

                @error('digital_file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    Disimpan di storage private. Tipe file: zip, rar, 7z, pdf, doc/docx, xls/xlsx, ppt/pptx, txt. Maksimal 100MB.
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="d-grid gap-2">
                    <button type="submit"
                            class="btn btn-primary rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                        <i data-feather="save" style="width: 15px; height: 15px;"></i>
                        <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}</span>
                    </button>

                    <a href="{{ route('admin.products.index') }}"
                       class="btn bg-secondary-subtle text-secondary rounded-pill d-inline-flex align-items-center justify-content-center gap-1">
                        <i data-feather="x" style="width: 15px; height: 15px;"></i>
                        <span>Batal</span>
                    </a>
                </div>

                <div class="alert alert-light border mt-3 mb-0">
                    <div class="fw-semibold mb-1">
                        Catatan
                    </div>

                    <div class="fs-13 text-muted">
                        Setelah produk disimpan, cek halaman public untuk memastikan tampilan dan harga sudah benar.
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
