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
            'hero_title' => 'Produk digital, template, aplikasi, dan tools AI untuk kerja lebih cepat.',
            'hero_subtitle' => 'Mulai dari Kantor Cerdas AI Kit, template administrasi, prompt AI, hingga aplikasi siap pakai untuk kebutuhan kerja profesional.',
            'primary_cta_text' => 'Lihat Produk',
            'primary_cta_url' => route('products.index'),
            'secondary_cta_text' => 'Cara Beli',
            'secondary_cta_url' => '#cara-beli',
            'support_title' => 'Butuh bantuan?',
            'support_text' => 'Hubungi tim Ruang Cerdas untuk pertanyaan produk, pembayaran, dan akses download.',
            'support_whatsapp' => null,
            'featured_section_title' => 'Pilihan terbaik dari Ruang Cerdas',
            'featured_section_subtitle' => 'Produk digital yang dirancang untuk membantu pekerjaan kantor, bisnis, dan produktivitas.',
            'footer_short_text' => 'Ruang Cerdas - Produk digital untuk kerja lebih cepat dan rapi.',
            'seo_title' => 'Ruang Cerdas - Produk Digital Siap Pakai',
            'seo_description' => 'Marketplace produk digital seperti template, ebook, file ZIP, dan aplikasi siap pakai.',
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
