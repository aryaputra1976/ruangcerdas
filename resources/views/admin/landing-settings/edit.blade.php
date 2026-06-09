@extends('layouts.admin')

@php
    $title = 'Landing Page Settings';
    $subtitle = 'Kelola konten utama halaman beranda public.';
@endphp

@section('content')

@php
    $visitCards = [
        ['label' => 'Kunjungan Hari Ini', 'value' => number_format((int) ($visitSummary['today_views'] ?? 0), 0, ',', '.'), 'icon' => 'activity', 'class' => 'primary'],
        ['label' => 'Pengunjung Unik Hari Ini', 'value' => number_format((int) ($visitSummary['today_visitors'] ?? 0), 0, ',', '.'), 'icon' => 'users', 'class' => 'success'],
        ['label' => 'Kunjungan ' . ($visitSummary['period_label'] ?? '7 Hari Terakhir'), 'value' => number_format((int) ($visitSummary['period_views'] ?? 0), 0, ',', '.'), 'icon' => 'bar-chart-2', 'class' => 'info'],
        ['label' => 'Pengunjung Unik ' . ($visitSummary['period_label'] ?? '7 Hari Terakhir'), 'value' => number_format((int) ($visitSummary['period_visitors'] ?? 0), 0, ',', '.'), 'icon' => 'trending-up', 'class' => 'warning'],
    ];
    $periodOptions = [
        'today' => 'Hari Ini',
        '7d' => '7 Hari',
        '30d' => '30 Hari',
    ];
@endphp

<div class="row g-3 mb-3">
    @foreach ($visitCards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="card rc-dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 border border-{{ $card['class'] }} border-opacity-10 bg-{{ $card['class'] }}-subtle rounded-3">
                            <div class="bg-{{ $card['class'] }} rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i data-feather="{{ $card['icon'] }}" class="text-white" style="width: 17px; height: 17px;"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted fs-13 mb-1">{{ $card['label'] }}</p>
                            <h4 class="mb-0 text-dark">{{ $card['value'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-3">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h5 class="card-title mb-1">Ringkasan Pengunjung Website</h5>
                        <p class="text-muted mb-0 fs-13">
                            Data ini berasal dari tracking internal Laravel pada halaman publik berbentuk HTML. Angka bisa berbeda dengan GA4 karena metode pengukuran berbeda.
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach ($periodOptions as $periodKey => $periodLabel)
                            <a href="{{ route('admin.landing-settings.edit', ['period' => $periodKey]) }}"
                               class="btn btn-sm {{ ($visitSummary['period'] ?? '7d') === $periodKey ? 'btn-primary' : 'bg-light border' }} rounded-pill px-3">
                                {{ $periodLabel }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (collect($visitSummary['daily_views'] ?? [])->isNotEmpty())
                    <div class="row g-2 mb-4">
                        @foreach ($visitSummary['daily_views'] as $daily)
                            @php
                                $barHeight = max(10, (int) round(((int) $daily['total_views'] / max(1, (int) ($visitSummary['max_daily_views'] ?? 1))) * 110));
                            @endphp
                            <div class="col">
                                <div class="border rounded-3 p-2 h-100 d-flex flex-column justify-content-end align-items-center bg-light-subtle">
                                    <div class="fw-semibold text-dark fs-13 mb-2">{{ number_format((int) $daily['total_views'], 0, ',', '.') }}</div>
                                    <div class="bg-primary rounded-top w-100" style="height: {{ $barHeight }}px; min-width: 18px;"></div>
                                    <div class="text-muted fs-12 mt-2">{{ $daily['label'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="table-responsive table-card">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-end">Kunjungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($visitSummary['daily_views'] as $daily)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Carbon::parse($daily['date'])->translatedFormat('d M Y') }}</td>
                                        <td class="text-end fw-semibold">{{ number_format((int) $daily['total_views'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i data-feather="inbox" class="text-muted mb-2" style="width: 40px; height: 40px;"></i>
                        <p class="text-muted mb-0">Belum ada kunjungan publik yang tercatat.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">Halaman Paling Sering Dibuka</h5>
                <p class="text-muted mb-0 fs-13">Top 5 halaman publik untuk periode yang sedang dipilih.</p>
            </div>
            <div class="card-body">
                @if (collect($visitSummary['top_pages'] ?? [])->isNotEmpty())
                    <ul class="list-group list-group-flush list-group-no-gutters">
                        @foreach ($visitSummary['top_pages'] as $page)
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between gap-3">
                                    <span class="text-dark fw-medium">{{ $page->path }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ number_format((int) $page->total_views, 0, ',', '.') }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4">
                        <i data-feather="file-text" class="text-muted mb-2" style="width: 40px; height: 40px;"></i>
                        <p class="text-muted mb-0">Belum ada halaman yang bisa diringkas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-1">Sumber Kunjungan</h5>
        <p class="text-muted mb-0 fs-13">Domain referer yang paling sering mengirim pengunjung ke website pada periode ini.</p>
    </div>
    <div class="card-body">
        @if (collect($visitSummary['top_referrers'] ?? [])->isNotEmpty())
            <div class="table-responsive table-card">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Sumber</th>
                            <th class="text-end">Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitSummary['top_referrers'] as $referrer)
                            <tr>
                                <td class="fw-medium text-dark">{{ $referrer->source }}</td>
                                <td class="text-end fw-semibold">{{ number_format((int) $referrer->total_visits, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i data-feather="share-2" class="text-muted mb-2" style="width: 40px; height: 40px;"></i>
                <p class="text-muted mb-0">Belum ada referer yang tercatat untuk periode ini.</p>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-1">Pengaturan Konten Landing</h5>
        <p class="text-muted mb-0 fs-13">
            Atur teks hero, CTA, bagian support, dan bagian produk unggulan.
        </p>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.landing-settings.update') }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="hero_badge" class="form-label">Hero Badge</label>
                    <input type="text" id="hero_badge" name="hero_badge"
                           class="form-control @error('hero_badge') is-invalid @enderror"
                           value="{{ old('hero_badge', $landingSetting->hero_badge) }}">
                    @error('hero_badge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="hero_title" class="form-label">Hero Title</label>
                    <input type="text" id="hero_title" name="hero_title"
                           class="form-control @error('hero_title') is-invalid @enderror"
                           value="{{ old('hero_title', $landingSetting->hero_title) }}">
                    @error('hero_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
                    <textarea id="hero_subtitle" name="hero_subtitle" rows="3"
                              class="form-control @error('hero_subtitle') is-invalid @enderror">{{ old('hero_subtitle', $landingSetting->hero_subtitle) }}</textarea>
                    @error('hero_subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="primary_cta_text" class="form-label">Primary CTA Text</label>
                    <input type="text" id="primary_cta_text" name="primary_cta_text"
                           class="form-control @error('primary_cta_text') is-invalid @enderror"
                           value="{{ old('primary_cta_text', $landingSetting->primary_cta_text) }}">
                    @error('primary_cta_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="primary_cta_url" class="form-label">Primary CTA URL</label>
                    <input type="text" id="primary_cta_url" name="primary_cta_url"
                           class="form-control @error('primary_cta_url') is-invalid @enderror"
                           value="{{ old('primary_cta_url', $landingSetting->primary_cta_url) }}">
                    @error('primary_cta_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="secondary_cta_text" class="form-label">Secondary CTA Text</label>
                    <input type="text" id="secondary_cta_text" name="secondary_cta_text"
                           class="form-control @error('secondary_cta_text') is-invalid @enderror"
                           value="{{ old('secondary_cta_text', $landingSetting->secondary_cta_text) }}">
                    @error('secondary_cta_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="secondary_cta_url" class="form-label">Secondary CTA URL</label>
                    <input type="text" id="secondary_cta_url" name="secondary_cta_url"
                           class="form-control @error('secondary_cta_url') is-invalid @enderror"
                           value="{{ old('secondary_cta_url', $landingSetting->secondary_cta_url) }}">
                    @error('secondary_cta_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="support_title" class="form-label">Support Title</label>
                    <input type="text" id="support_title" name="support_title"
                           class="form-control @error('support_title') is-invalid @enderror"
                           value="{{ old('support_title', $landingSetting->support_title) }}">
                    @error('support_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="support_whatsapp" class="form-label">Support WhatsApp</label>
                    <input type="text" id="support_whatsapp" name="support_whatsapp"
                           class="form-control @error('support_whatsapp') is-invalid @enderror"
                           value="{{ old('support_whatsapp', $landingSetting->support_whatsapp) }}"
                           placeholder="Contoh: 081234567890">
                    <small class="text-muted d-block mt-1">Boleh isi format 08xxx atau 62xxx. Sistem otomatis konversi ke link `wa.me`.</small>
                    @error('support_whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="support_text" class="form-label">Support Text</label>
                    <textarea id="support_text" name="support_text" rows="3"
                              class="form-control @error('support_text') is-invalid @enderror">{{ old('support_text', $landingSetting->support_text) }}</textarea>
                    @error('support_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="featured_section_title" class="form-label">Featured Section Title</label>
                    <input type="text" id="featured_section_title" name="featured_section_title"
                           class="form-control @error('featured_section_title') is-invalid @enderror"
                           value="{{ old('featured_section_title', $landingSetting->featured_section_title) }}">
                    @error('featured_section_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="footer_short_text" class="form-label">Footer Short Text</label>
                    <input type="text" id="footer_short_text" name="footer_short_text"
                           class="form-control @error('footer_short_text') is-invalid @enderror"
                           value="{{ old('footer_short_text', $landingSetting->footer_short_text) }}">
                    @error('footer_short_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="featured_section_subtitle" class="form-label">Featured Section Subtitle</label>
                    <textarea id="featured_section_subtitle" name="featured_section_subtitle" rows="3"
                              class="form-control @error('featured_section_subtitle') is-invalid @enderror">{{ old('featured_section_subtitle', $landingSetting->featured_section_subtitle) }}</textarea>
                    @error('featured_section_subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mt-3">
                    <hr>
                    <h5 class="mb-1">SEO Dasar</h5>
                    <p class="text-muted fs-13 mb-0">Dipakai sebagai default SEO halaman public.</p>
                </div>

                <div class="col-md-6">
                    <label for="seo_title" class="form-label">SEO Title</label>
                    <input type="text" id="seo_title" name="seo_title"
                           class="form-control @error('seo_title') is-invalid @enderror"
                           value="{{ old('seo_title', $landingSetting->seo_title) }}">
                    @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="seo_keywords" class="form-label">SEO Keywords</label>
                    <input type="text" id="seo_keywords" name="seo_keywords"
                           class="form-control @error('seo_keywords') is-invalid @enderror"
                           value="{{ old('seo_keywords', $landingSetting->seo_keywords) }}">
                    @error('seo_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="seo_description" class="form-label">SEO Description</label>
                    <textarea id="seo_description" name="seo_description" rows="3"
                              class="form-control @error('seo_description') is-invalid @enderror">{{ old('seo_description', $landingSetting->seo_description) }}</textarea>
                    @error('seo_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="og_image_url" class="form-label">OG Image URL</label>
                    <input type="text" id="og_image_url" name="og_image_url"
                           class="form-control @error('og_image_url') is-invalid @enderror"
                           value="{{ old('og_image_url', $landingSetting->og_image_url) }}">
                    @error('og_image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mt-3">
                    <hr>
                    <h5 class="mb-1">Tracking & WhatsApp CTA</h5>
                    <p class="text-muted fs-13 mb-0">Kosongkan jika belum digunakan.</p>
                </div>

                <div class="col-md-4">
                    <label for="meta_pixel_id" class="form-label">Meta Pixel ID</label>
                    <input type="text" id="meta_pixel_id" name="meta_pixel_id"
                           class="form-control @error('meta_pixel_id') is-invalid @enderror"
                           value="{{ old('meta_pixel_id', $landingSetting->meta_pixel_id) }}">
                    @error('meta_pixel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="google_analytics_id" class="form-label">Google Analytics ID (GA4)</label>
                    <input type="text" id="google_analytics_id" name="google_analytics_id"
                           class="form-control @error('google_analytics_id') is-invalid @enderror"
                           value="{{ old('google_analytics_id', $landingSetting->google_analytics_id) }}">
                    @error('google_analytics_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="google_tag_manager_id" class="form-label">Google Tag Manager ID</label>
                    <input type="text" id="google_tag_manager_id" name="google_tag_manager_id"
                           class="form-control @error('google_tag_manager_id') is-invalid @enderror"
                           value="{{ old('google_tag_manager_id', $landingSetting->google_tag_manager_id) }}">
                    @error('google_tag_manager_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="whatsapp_cta_text" class="form-label">WhatsApp CTA Text</label>
                    <input type="text" id="whatsapp_cta_text" name="whatsapp_cta_text"
                           class="form-control @error('whatsapp_cta_text') is-invalid @enderror"
                           value="{{ old('whatsapp_cta_text', $landingSetting->whatsapp_cta_text) }}"
                           placeholder="Contoh: Tanya via WhatsApp">
                    @error('whatsapp_cta_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="whatsapp_default_message" class="form-label">WhatsApp Default Message</label>
                    <textarea id="whatsapp_default_message" name="whatsapp_default_message" rows="3"
                              class="form-control @error('whatsapp_default_message') is-invalid @enderror"
                              placeholder="Contoh: Halo Ruang Cerdas, saya tertarik dengan produk ini.">{{ old('whatsapp_default_message', $landingSetting->whatsapp_default_message) }}</textarea>
                    @error('whatsapp_default_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

@endsection
