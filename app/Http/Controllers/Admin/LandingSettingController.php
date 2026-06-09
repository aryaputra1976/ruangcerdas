<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use App\Models\PageVisit;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LandingSettingController extends Controller
{
    public function edit(Request $request)
    {
        $landingSetting = LandingSetting::query()->firstOrCreate([]);
        $period = $request->string('period')->lower()->value();
        $period = in_array($period, ['today', '7d', '30d'], true) ? $period : '7d';
        $today = now()->toDateString();
        $startDate = match ($period) {
            'today' => $today,
            '30d' => now()->copy()->subDays(29)->toDateString(),
            default => now()->copy()->subDays(6)->toDateString(),
        };
        $rangeQuery = PageVisit::query()
            ->whereDate('visited_on', '>=', $startDate)
            ->whereDate('visited_on', '<=', $today);

        $dailyViews = $rangeQuery
            ->clone()
            ->selectRaw('visited_on, count(*) as total_views')
            ->groupBy('visited_on')
            ->orderBy('visited_on')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->visited_on)->toDateString());

        $periodDates = collect(Carbon::parse($startDate)->daysUntil(Carbon::parse($today)->addDay()))
            ->map(fn (Carbon $date) => $date->toDateString());

        $dailySeries = $periodDates
            ->map(function (string $date) use ($dailyViews) {
                $views = (int) ($dailyViews->get($date)->total_views ?? 0);

                return [
                    'date' => $date,
                    'label' => Carbon::parse($date)->translatedFormat('d M'),
                    'total_views' => $views,
                ];
            });

        $maxDailyViews = max(1, (int) $dailySeries->max('total_views'));
        $topReferrers = $rangeQuery
            ->clone()
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->get(['referer'])
            ->map(function (PageVisit $visit) {
                $host = parse_url((string) $visit->referer, PHP_URL_HOST);
                $source = is_string($host) && $host !== '' ? preg_replace('/^www\./', '', strtolower($host)) : 'Direct / Unknown';

                return $source ?: 'Direct / Unknown';
            })
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->map(fn ($count, $source) => (object) ['source' => $source, 'total_visits' => $count])
            ->values();

        $visitSummary = [
            'today_views' => PageVisit::query()->whereDate('visited_on', $today)->count(),
            'today_visitors' => PageVisit::query()->whereDate('visited_on', $today)->distinct('visitor_hash')->count('visitor_hash'),
            'period' => $period,
            'period_label' => match ($period) {
                'today' => 'Hari Ini',
                '30d' => '30 Hari Terakhir',
                default => '7 Hari Terakhir',
            },
            'period_views' => $rangeQuery
                ->clone()
                ->count(),
            'period_visitors' => $rangeQuery
                ->clone()
                ->distinct('visitor_hash')
                ->count('visitor_hash'),
            'top_pages' => $rangeQuery
                ->clone()
                ->selectRaw('path, count(*) as total_views')
                ->groupBy('path')
                ->orderByDesc('total_views')
                ->limit(5)
                ->get(),
            'top_referrers' => $topReferrers,
            'daily_views' => $dailySeries->all(),
            'max_daily_views' => $maxDailyViews,
        ];

        return view('admin.landing-settings.edit', compact('landingSetting', 'visitSummary'));
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
