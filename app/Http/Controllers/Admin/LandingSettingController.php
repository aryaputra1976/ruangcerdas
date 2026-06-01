<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class LandingSettingController extends Controller
{
    public function edit()
    {
        $landingSetting = LandingSetting::query()->firstOrCreate([]);

        return view('admin.landing-settings.edit', compact('landingSetting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'hero_badge' => ['nullable', 'string', 'max:100'],
            'primary_cta_text' => ['nullable', 'string', 'max:100'],
            'primary_cta_url' => ['nullable', 'string', 'max:255'],
            'secondary_cta_text' => ['nullable', 'string', 'max:100'],
            'secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'support_title' => ['nullable', 'string', 'max:255'],
            'support_text' => ['nullable', 'string', 'max:1000'],
            'support_whatsapp' => ['nullable', 'string', 'max:30'],
            'featured_section_title' => ['nullable', 'string', 'max:255'],
            'featured_section_subtitle' => ['nullable', 'string', 'max:1000'],
            'footer_short_text' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'og_image_url' => ['nullable', 'string', 'max:255'],
            'meta_pixel_id' => ['nullable', 'string', 'max:100'],
            'google_analytics_id' => ['nullable', 'string', 'max:100'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:100'],
            'whatsapp_cta_text' => ['nullable', 'string', 'max:100'],
            'whatsapp_default_message' => ['nullable', 'string', 'max:1000'],
        ]);

        $landingSetting = LandingSetting::query()->firstOrCreate([]);
        $landingSetting->update($validated);

        ActivityLogger::log(
            'landing_settings.updated',
            $landingSetting,
            'Admin memperbarui konten landing page.'
        );

        return redirect()
            ->route('admin.landing-settings.edit')
            ->with('success', 'Pengaturan landing page berhasil diperbarui.');
    }
}
