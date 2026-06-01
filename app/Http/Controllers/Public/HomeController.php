<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use App\Models\Product;
use App\Models\Testimonial;
use App\Services\PricingService;

class HomeController extends Controller
{
    public function index(PricingService $pricingService)
    {
        $defaultLanding = [
            'hero_badge' => 'Ruang Cerdas',
            'hero_title' => 'Produk Digital dan Belajar Online untuk Kerja Lebih Cerdas',
            'hero_subtitle' => 'Temukan eBook, template, tools AI, aplikasi siap pakai, serta paket belajar dan tryout online untuk membantu pekerjaan, bisnis, dan persiapan karier secara lebih terarah.',
            'primary_cta_text' => 'Lihat Produk',
            'primary_cta_url' => route('products.index'),
            'secondary_cta_text' => 'Lihat Tryout',
            'secondary_cta_url' => '#tryout',
            'support_title' => 'Butuh bantuan?',
            'support_text' => 'Hubungi tim Ruang Cerdas untuk pertanyaan produk, pembayaran, dan akses download.',
            'support_whatsapp' => null,
            'featured_section_title' => 'Produk dan Paket Digital Unggulan',
            'featured_section_subtitle' => 'Pilihan eBook, template, tools AI, aplikasi, dan paket belajar digital untuk membantu kerja dan belajar lebih terarah.',
            'footer_short_text' => 'Ruang Cerdas - Produk digital dan belajar online untuk kerja lebih cerdas.',
            'seo_title' => 'Ruang Cerdas - Produk Digital & Belajar Online',
            'seo_description' => 'Ruang Cerdas menyediakan eBook, template, tools AI, aplikasi siap pakai, paket belajar digital, dan persiapan tryout online.',
            'seo_keywords' => null,
            'og_image_url' => null,
        ];

        $landingSetting = LandingSetting::query()->first();

        $landing = array_merge($defaultLanding, array_filter([
            'hero_badge' => $landingSetting?->hero_badge,
            'hero_title' => $landingSetting?->hero_title,
            'hero_subtitle' => $landingSetting?->hero_subtitle,
            'primary_cta_text' => $landingSetting?->primary_cta_text,
            'primary_cta_url' => $landingSetting?->primary_cta_url,
            'secondary_cta_text' => $landingSetting?->secondary_cta_text,
            'secondary_cta_url' => $landingSetting?->secondary_cta_url,
            'support_title' => $landingSetting?->support_title,
            'support_text' => $landingSetting?->support_text,
            'support_whatsapp' => $landingSetting?->support_whatsapp,
            'featured_section_title' => $landingSetting?->featured_section_title,
            'featured_section_subtitle' => $landingSetting?->featured_section_subtitle,
            'footer_short_text' => $landingSetting?->footer_short_text,
            'seo_title' => $landingSetting?->seo_title,
            'seo_description' => $landingSetting?->seo_description,
            'seo_keywords' => $landingSetting?->seo_keywords,
            'og_image_url' => $landingSetting?->og_image_url,
        ], fn ($value) => filled($value)));

        $featuredProducts = Product::query()
            ->with('category')
            ->visibleToPublic()
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(18)
            ->get();

        $featuredProducts = $featuredProducts
            ->filter(fn (Product $product) => $product->isVisibleToPublic())
            ->take(6)
            ->values();

        $featuredProducts->transform(function ($product) use ($pricingService) {
            $product->pricing = $pricingService->resolve($product);

            return $product;
        });

        $testimonials = Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->take(6)
            ->get();

        return view('public.home', compact('featuredProducts', 'landing', 'testimonials'));
    }
}
