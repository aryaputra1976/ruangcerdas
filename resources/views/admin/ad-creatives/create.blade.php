@extends('layouts.admin')

@php
    $isEdit = isset($creative) && $creative;
    $title = $isEdit ? 'Edit Iklan' : 'Buat Iklan';
    $subtitle = $isEdit
        ? 'Perbarui konten creative lalu generate ulang gambar 9:16 tanpa membuat record baru.'
        : 'Buat gambar iklan PNG 9:16 bergaya viral note untuk produk atau kampanye umum.';
    $generationMode = request('mode') === 'bulk' ? 'bulk' : old('mode', 'single');
    if ($isEdit) {
        $generationMode = 'single';
    }
    $selectedProductId = old('product_id', $creative->product_id ?? null);
    $selectedTemplate = old('template_key', $creative->template_key ?? 'viral_note');
    $selectedSizePreset = old('size_preset', ($creative->width ?? 1080) === 1080 && ($creative->height ?? 1920) === 1350 ? 'feed_portrait' : ((($creative->width ?? 1080) === 1080 && ($creative->height ?? 1920) === 1080) ? 'square' : 'story'));
    $generateAllSizes = (bool) old('generate_all_sizes', false);
    $titleValue = old('title', $creative->title ?? 'Catatan Viral');
    $headlineValue = old('headline', $creative->headline ?? ($generationMode === 'bulk' ? 'Calon pembeli sering berhenti karena belum tahu isi {product}. Ini versi yang lebih jelas dan ringkas.' : ''));
    $bodyValue = old('body', $creative->body ?? ($generationMode === 'bulk' ? '{product} cocok untuk calon pembeli yang butuh materi lebih praktis. Harga mulai {price} dan formatnya siap dipakai belajar mandiri.' : ''));
    $bulletValue = old('bullets', isset($creative) && ! empty($creative->bullets) ? implode("\n", $creative->bullets) : '');
    $ctaValue = old('cta_text', $creative->cta_text ?? 'Ambil Sekarang');
    $brandValue = old('brand_text', $creative->brand_text ?? 'ruangcerdas.id');
    $actions = new \Illuminate\Support\HtmlString(
        '<a href="' . route('admin.ad-creatives.index') . '" class="btn btn-light border rounded-pill px-4">Kembali</a>'
    );
@endphp

@push('styles')
<style>
    .ad-preview-shell {
        background:
            radial-gradient(circle at top left, rgba(255, 255, 255, 0.72), transparent 35%),
            linear-gradient(180deg, #f7ede0 0%, #efe2cf 100%);
        border: 1px solid #ead9bf;
        border-radius: 24px;
        padding: 18px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
    }

    .ad-preview-frame {
        width: min(100%, 320px);
        margin: 0 auto;
        border-radius: 24px;
        padding: 18px;
        background: repeating-linear-gradient(
            to bottom,
            rgba(171, 138, 112, 0.10) 0,
            rgba(171, 138, 112, 0.10) 1px,
            transparent 1px,
            transparent 50px
        );
        transition: background-color .2s ease, transform .2s ease;
        box-shadow: 0 20px 50px rgba(102, 72, 51, 0.16);
    }

    .ad-preview-frame[data-size="story"] {
        aspect-ratio: 9 / 16;
    }

    .ad-preview-frame[data-size="feed_portrait"] {
        aspect-ratio: 4 / 5;
    }

    .ad-preview-frame[data-size="square"] {
        aspect-ratio: 1 / 1;
    }

    .ad-preview-frame[data-template="viral_note"] {
        background-color: #f7ede0;
        color: #27231f;
    }

    .ad-preview-frame[data-template="urgent_offer"] {
        background-color: #f8ece1;
        color: #2c1f18;
    }

    .ad-preview-frame[data-template="social_proof"] {
        background-color: #f3ede1;
        color: #222629;
    }

    .ad-preview-header {
        background: rgba(193, 43, 43, 0.14);
        border-radius: 16px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }

    .ad-preview-frame[data-template="urgent_offer"] .ad-preview-header {
        background: rgba(175, 34, 34, 0.16);
    }

    .ad-preview-frame[data-template="social_proof"] .ad-preview-header {
        background: rgba(184, 55, 55, 0.14);
    }

    .ad-preview-title {
        color: #bf2b2b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .ad-preview-frame[data-template="urgent_offer"] .ad-preview-title {
        color: #af2222;
    }

    .ad-preview-frame[data-template="social_proof"] .ad-preview-title {
        color: #b83737;
    }

    .ad-preview-headline {
        font-size: 20px;
        line-height: 1.18;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
    }

    .ad-preview-product {
        margin-top: 10px;
        color: #78685d;
        font-size: 13px;
        font-weight: 600;
    }

    .ad-preview-note {
        background: rgba(255, 251, 245, 0.94);
        border: 1px solid rgba(199, 176, 152, 0.55);
        border-radius: 18px;
        padding: 16px 15px;
        min-height: 42%;
    }

    .ad-preview-label {
        color: #bf2b2b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .ad-preview-body {
        font-size: 13px;
        line-height: 1.6;
        color: #3d3933;
        margin-bottom: 12px;
    }

    .ad-preview-bullets {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 8px;
    }

    .ad-preview-bullets li {
        position: relative;
        padding-left: 18px;
        font-size: 12px;
        line-height: 1.5;
        color: #302c27;
    }

    .ad-preview-bullets li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 7px;
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #bf2b2b;
    }

    .ad-preview-price {
        display: inline-block;
        margin-top: 14px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(193, 43, 43, 0.12);
        color: #bf2b2b;
        font-size: 12px;
        font-weight: 800;
    }

    .ad-preview-cta {
        margin-top: 16px;
        background: #bf2b2b;
        color: #fff;
        text-align: center;
        border-radius: 16px;
        padding: 11px 14px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .ad-preview-frame[data-template="urgent_offer"] .ad-preview-cta {
        background: #af2222;
    }

    .ad-preview-frame[data-template="social_proof"] .ad-preview-cta {
        background: #b83737;
    }

    .ad-preview-footer {
        margin-top: 14px;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        font-size: 11px;
        color: #75685d;
        align-items: flex-end;
    }

    .ad-preview-brand {
        font-weight: 800;
        color: inherit;
    }

    .ad-preview-meta {
        text-align: right;
        font-size: 10px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .ad-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .ad-preview-size-card {
        border: 1px solid #ead9bf;
        border-radius: 16px;
        padding: 12px;
        background: #fffdf9;
    }

    .ad-preview-size-card.active {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .ad-preview-size-thumb {
        margin: 0 auto 10px;
        width: 58px;
        border-radius: 12px;
        background: linear-gradient(180deg, #f2e8d8 0%, #f9f5ed 100%);
        border: 1px solid #dfcfb8;
    }

    .ad-preview-size-thumb[data-size="story"] {
        aspect-ratio: 9 / 16;
    }

    .ad-preview-size-thumb[data-size="feed_portrait"] {
        aspect-ratio: 4 / 5;
    }

    .ad-preview-size-thumb[data-size="square"] {
        aspect-ratio: 1 / 1;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-1">Form Generator Iklan</h5>
        <p class="text-muted mb-0 fs-13">{{ $isEdit ? 'Perbarui teks, template, atau produk lalu sistem akan generate ulang file gambar.' : 'Pilih produk opsional lalu susun headline, manfaat, dan CTA untuk menghasilkan creative baru.' }}</p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('admin.ad-creatives.update', $creative) : route('admin.ad-creatives.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="mode" id="mode" value="{{ $generationMode }}">

            <div class="row g-3">
                <div class="col-12 {{ $isEdit ? 'd-none' : '' }}">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="fw-semibold text-dark mb-2">Mode Generate</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.ad-creatives.create') }}" class="btn {{ $generationMode === 'single' ? 'btn-primary' : 'btn-light border' }} rounded-pill px-4">Single</a>
                            <a href="{{ route('admin.ad-creatives.create', ['mode' => 'bulk']) }}" class="btn {{ $generationMode === 'bulk' ? 'btn-primary' : 'btn-light border' }} rounded-pill px-4">Massal</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="product_id" class="form-label">Produk</label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                        <option value="">Tanpa Produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) $selectedProductId === (string) $product->id)>
                                {{ $product->name }} - Rp{{ number_format((int) $product->public_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                        <small class="text-muted mb-0">Memilih produk akan memunculkan saran headline, body, dan bullet yang bisa Anda edit lagi.</small>
                        <button type="button" id="refill-from-product" class="btn btn-sm btn-light border rounded-pill px-3">
                            Isi Ulang dari Produk
                        </button>
                    </div>
                    @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 {{ $generationMode === 'bulk' ? '' : 'd-none' }}" id="bulk-products-wrapper">
                    <label for="product_ids" class="form-label">Produk Massal</label>
                    <select name="product_ids[]" id="product_ids" class="form-select @error('product_ids') is-invalid @enderror" multiple size="6">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(collect(old('product_ids', []))->contains($product->id))>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">Pilih beberapa produk. Placeholder `{product}` dan `{price}` pada body akan diganti otomatis per produk.</small>
                    @error('product_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="template_key" class="form-label">Template</label>
                    <select name="template_key" id="template_key" class="form-select @error('template_key') is-invalid @enderror">
                        @foreach ($templates as $templateKey => $templateLabel)
                            <option value="{{ $templateKey }}" @selected($selectedTemplate === $templateKey)>{{ $templateLabel }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">Preset warna dan nuansa CTA akan mengikuti template yang dipilih.</small>
                    @error('template_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="size_preset" class="form-label">Ukuran</label>
                    <select name="size_preset" id="size_preset" class="form-select @error('size_preset') is-invalid @enderror">
                        @foreach ($sizePresets as $sizeKey => $sizeLabel)
                            <option value="{{ $sizeKey }}" @selected($selectedSizePreset === $sizeKey)>{{ $sizeLabel }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1" id="size-preset-help">Pilih ukuran output untuk Story, Feed Portrait, atau Square.</small>
                    @error('size_preset')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="format" class="form-label">Format</label>
                    <input type="text" id="format" name="format" value="png" class="form-control" readonly>
                    <small class="text-muted d-block mt-1">Master file disimpan sebagai PNG. Download JPG/WebP tersedia di halaman detail.</small>
                </div>

                <div class="col-md-3">
                    <label for="title" class="form-label">Title Kecil</label>
                    <input type="text" id="title" name="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ $titleValue }}"
                           placeholder="Contoh: Catatan Viral">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 {{ $isEdit ? 'd-none' : '' }}">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="generate_all_sizes" name="generate_all_sizes" @checked($generateAllSizes)>
                            <label class="form-check-label fw-semibold text-dark" for="generate_all_sizes">
                                Buat semua ukuran sekaligus
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1">Jika aktif, sistem akan membuat `Story`, `Feed Portrait`, dan `Square` dalam satu submit.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="cta_text" class="form-label">CTA Text</label>
                    <input type="text" id="cta_text" name="cta_text"
                           class="form-control @error('cta_text') is-invalid @enderror"
                           value="{{ $ctaValue }}"
                           placeholder="Contoh: Ambil Sekarang">
                    @error('cta_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="headline" class="form-label">Headline</label>
                    <textarea id="headline" name="headline" rows="3"
                              class="form-control @error('headline') is-invalid @enderror"
                              placeholder="Headline besar untuk iklan">{{ $headlineValue }}</textarea>
                    @error('headline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="body" class="form-label">Body</label>
                    <textarea id="body" name="body" rows="4"
                              class="form-control @error('body') is-invalid @enderror"
                              placeholder="Isi catatan utama">{{ $bodyValue }}</textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="bullets" class="form-label">Bullet Manfaat</label>
                    <textarea id="bullets" name="bullets" rows="6"
                              class="form-control @error('bullets') is-invalid @enderror"
                              placeholder="Satu manfaat per baris">{{ $bulletValue }}</textarea>
                    <small class="text-muted d-block mt-1">Maksimal 5 bullet. Tulis satu manfaat per baris.</small>
                    @error('bullets')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="brand_text" class="form-label">Brand Text</label>
                    <input type="text" id="brand_text" name="brand_text"
                           class="form-control @error('brand_text') is-invalid @enderror"
                           value="{{ $brandValue }}"
                           placeholder="Contoh: ruangcerdas.id">
                    @error('brand_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="card border shadow-sm mb-0">
                        <div class="card-header bg-light-subtle">
                            <h5 class="card-title mb-1">Preview Sebelum Generate</h5>
                            <p class="text-muted mb-0 fs-13">Preview ini menyesuaikan template, ukuran, dan isi teks yang sedang Anda edit.</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-start">
                                <div class="col-xl-5">
                                    <div class="ad-preview-shell">
                                        <div class="ad-preview-frame" id="ad-preview-frame" data-template="{{ $selectedTemplate }}" data-size="{{ $selectedSizePreset }}">
                                            <div class="ad-preview-header">
                                                <div class="ad-preview-title" id="preview-title">{{ $titleValue }}</div>
                                                <p class="ad-preview-headline" id="preview-headline">{{ $headlineValue }}</p>
                                                <div class="ad-preview-product" id="preview-product">{{ $selectedProductId ? ($products->firstWhere('id', (int) $selectedProductId)?->name ?? 'Tanpa Produk') : 'Tanpa Produk' }}</div>
                                            </div>

                                            <div class="ad-preview-note">
                                                <div class="ad-preview-label" id="preview-label">Catatan Penting</div>
                                                <div class="ad-preview-body" id="preview-body">{{ $bodyValue }}</div>
                                                <ul class="ad-preview-bullets" id="preview-bullets"></ul>
                                                <div class="ad-preview-price" id="preview-price">Mulai Rp49.000</div>
                                                <div class="ad-preview-cta" id="preview-cta">{{ $ctaValue }}</div>
                                            </div>

                                            <div class="ad-preview-footer">
                                                <div class="ad-preview-brand" id="preview-brand">{{ $brandValue }}</div>
                                                <div class="ad-preview-meta" id="preview-meta">{{ $selectedSizePreset }}<br>{{ $selectedTemplate }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-7">
                                    <div class="border rounded-3 p-3 bg-light-subtle">
                                        <div class="fw-semibold text-dark mb-1">Paket Preview Ukuran</div>
                                        <div class="text-muted fs-13">Kalau opsi semua ukuran aktif, ketiga rasio ini akan dibuat sekaligus.</div>

                                        <div class="ad-preview-grid" id="size-preview-grid">
                                            @foreach ($sizePresets as $sizeKey => $sizeLabel)
                                                <div class="ad-preview-size-card {{ $selectedSizePreset === $sizeKey ? 'active' : '' }}" data-size-card="{{ $sizeKey }}">
                                                    <div class="ad-preview-size-thumb" data-size="{{ $sizeKey }}"></div>
                                                    <div class="fw-semibold text-dark">{{ $sizeLabel }}</div>
                                                    <div class="text-muted fs-13">
                                                        @php
                                                            $preset = \App\Models\AdCreative::SIZE_PRESETS[$sizeKey];
                                                        @endphp
                                                        {{ $preset['width'] }} x {{ $preset['height'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="fw-semibold text-dark mb-2">Arah Visual Template</div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-semibold text-dark mb-1">Viral Note</div>
                                    <div class="text-muted fs-13">Gaya catatan edukatif dengan nuansa lembut dan cocok untuk soft selling.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-semibold text-dark mb-1">Urgent Offer</div>
                                    <div class="text-muted fs-13">Warna promo lebih kuat untuk diskon, kuota, atau penawaran terbatas.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-semibold text-dark mb-1">Social Proof</div>
                                    <div class="text-muted fs-13">Cocok untuk membangun trust, manfaat praktis, dan gaya konten viral.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-1">
                    <i data-feather="image" style="width: 14px; height: 14px;"></i>
                    <span>{{ $isEdit ? 'Simpan & Generate Ulang' : ($generationMode === 'bulk' ? 'Generate Massal' : 'Generate PNG') }}</span>
                </button>
                <a href="{{ route('admin.ad-creatives.index') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productSelect = document.getElementById('product_id');
        const modeField = document.getElementById('mode');
        const allSizesCheckbox = document.getElementById('generate_all_sizes');
        const sizePresetSelect = document.getElementById('size_preset');
        const sizePresetHelp = document.getElementById('size-preset-help');
        const refillFromProductButton = document.getElementById('refill-from-product');
        const previewFrame = document.getElementById('ad-preview-frame');
        const previewTitle = document.getElementById('preview-title');
        const previewHeadline = document.getElementById('preview-headline');
        const previewProduct = document.getElementById('preview-product');
        const previewLabel = document.getElementById('preview-label');
        const previewBody = document.getElementById('preview-body');
        const previewBullets = document.getElementById('preview-bullets');
        const previewPrice = document.getElementById('preview-price');
        const previewCta = document.getElementById('preview-cta');
        const previewBrand = document.getElementById('preview-brand');
        const previewMeta = document.getElementById('preview-meta');
        const sizeCards = document.querySelectorAll('[data-size-card]');
        const prefills = @json($productPrefills);
        const sizePresets = @json(\App\Models\AdCreative::SIZE_PRESETS);
        const fields = {
            title: document.getElementById('title'),
            headline: document.getElementById('headline'),
            body: document.getElementById('body'),
            bullets: document.getElementById('bullets'),
            cta_text: document.getElementById('cta_text'),
            brand_text: document.getElementById('brand_text'),
        };

        const placeholderBullets = [
            'Materi lebih ringkas dan cepat dipahami.',
            'Cocok untuk belajar mandiri kapan saja.',
            'Siap dipakai untuk konten promosi yang padat.',
        ];

        const updatePreview = function () {
            const selectedTemplate = document.getElementById('template_key')?.value || 'viral_note';
            const selectedSize = sizePresetSelect?.value || 'story';
            const selectedProduct = productSelect && productSelect.selectedOptions.length
                ? productSelect.selectedOptions[0].text.split(' - Rp')[0]
                : 'Tanpa Produk';
            const bullets = String(fields.bullets?.value || '')
                .split(/\r\n|\r|\n/)
                .map(function (item) { return item.trim(); })
                .filter(Boolean)
                .slice(0, 5);
            const bodyText = String(fields.body?.value || '').trim();
            const headlineText = String(fields.headline?.value || '').trim();
            const titleText = String(fields.title?.value || '').trim();
            const ctaText = String(fields.cta_text?.value || '').trim();
            const brandText = String(fields.brand_text?.value || '').trim();

            if (previewFrame) {
                previewFrame.dataset.template = selectedTemplate;
                previewFrame.dataset.size = selectedSize;
            }

            if (previewTitle) {
                previewTitle.textContent = titleText || 'Catatan Viral';
            }

            if (previewHeadline) {
                previewHeadline.textContent = headlineText || 'Preview headline akan tampil di sini saat Anda mulai mengetik.';
            }

            if (previewProduct) {
                previewProduct.textContent = selectedProduct || 'Tanpa Produk';
            }

            if (previewBody) {
                previewBody.textContent = bodyText || 'Isi body akan membantu admin melihat ritme teks utama sebelum gambar dibuat.';
            }

            if (previewBullets) {
                const finalBullets = bullets.length ? bullets : placeholderBullets;
                previewBullets.innerHTML = finalBullets
                    .map(function (bullet) { return '<li>' + bullet.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</li>'; })
                    .join('');
            }

            if (previewCta) {
                previewCta.textContent = ctaText || 'Ambil Sekarang';
            }

            if (previewBrand) {
                previewBrand.textContent = brandText || 'ruangcerdas.id';
            }

            if (previewLabel) {
                previewLabel.textContent = selectedTemplate === 'urgent_offer'
                    ? 'Jangan Lewatkan'
                    : (selectedTemplate === 'social_proof' ? 'Kenapa Banyak Yang Suka' : 'Catatan Penting');
            }

            if (previewMeta) {
                previewMeta.innerHTML = selectedSize.replace('_', ' ') + '<br>' + selectedTemplate.replace('_', ' ');
            }

            if (previewPrice) {
                const selectedPrefill = productSelect ? prefills[productSelect.value] : null;
                previewPrice.textContent = selectedPrefill && selectedPrefill.body && productSelect.selectedOptions.length
                    ? productSelect.selectedOptions[0].text.includes('Rp')
                        ? 'Mulai Rp' + productSelect.selectedOptions[0].text.split(' - Rp')[1]
                        : 'Mulai Rp49.000'
                    : 'Mulai Rp49.000';
            }

            sizeCards.forEach(function (card) {
                card.classList.toggle('active', card.dataset.sizeCard === selectedSize);
            });
        };

        const applyPrefill = function () {
            const selected = prefills[productSelect.value];

            if (!selected) {
                return;
            }

            Object.entries(fields).forEach(function ([key, field]) {
                if (!field || String(field.value || '').trim() !== '') {
                    return;
                }

                if (selected[key]) {
                    field.value = selected[key];
                }
            });
        };

        const forcePrefill = function () {
            const selected = prefills[productSelect.value];

            if (!selected) {
                return;
            }

            Object.entries(fields).forEach(function ([key, field]) {
                if (!field) {
                    return;
                }

                if (selected[key]) {
                    field.value = selected[key];
                }
            });

            updatePreview();
        };

        if (productSelect) {
            productSelect.addEventListener('change', applyPrefill);
            productSelect.addEventListener('change', updatePreview);
        }

        if (refillFromProductButton) {
            refillFromProductButton.addEventListener('click', forcePrefill);
        }

        if (allSizesCheckbox && sizePresetSelect) {
            const syncSizePreset = function () {
                if (sizePresetHelp) {
                    sizePresetHelp.textContent = allSizesCheckbox.checked
                        ? 'Pilihan ukuran ini akan diabaikan karena sistem membuat semua ukuran sekaligus.'
                        : 'Pilih ukuran output untuk Story, Feed Portrait, atau Square.';
                }
            };

            allSizesCheckbox.addEventListener('change', syncSizePreset);
            syncSizePreset();
        }

        Object.values(fields).forEach(function (field) {
            if (!field) {
                return;
            }

            field.addEventListener('input', updatePreview);
        });

        document.getElementById('template_key')?.addEventListener('change', updatePreview);
        sizePresetSelect?.addEventListener('change', updatePreview);

        if (modeField && modeField.value === 'bulk' && fields.bullets && String(fields.bullets.value || '').trim() === '') {
            fields.bullets.value = "Materi lebih ringkas dan mudah dipahami.\nBisa dipakai belajar mandiri kapan saja.\nHarga digital lebih praktis untuk langsung mulai.";
        }

        updatePreview();
    });
</script>
@endpush
