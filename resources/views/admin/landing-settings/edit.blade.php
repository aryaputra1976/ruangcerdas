@extends('layouts.admin')

@php
    $title = 'Landing Page Settings';
    $subtitle = 'Kelola konten utama halaman beranda public.';
@endphp

@section('content')

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
                           value="{{ old('support_whatsapp', $landingSetting->support_whatsapp) }}">
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
