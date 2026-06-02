<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\Article;
use App\Models\LeadMagnet;
use App\Services\PricingService;

class HomeController extends Controller
{
    public function index(PricingService $pricingService)
    {
        $defaultLanding = [
            'hero_badge' => 'Ruang Cerdas',
            'hero_title' => 'Produk Digital Praktis untuk Belajar, Kerja, dan Seleksi ASN',
            'hero_subtitle' => 'Ruang Cerdas menyediakan eBook, checklist, template, dan panduan siap pakai untuk membantu Anda belajar lebih terarah, bekerja lebih rapi, dan menyiapkan administrasi dengan lebih mudah.',
            'primary_cta_text' => 'Lihat Produk',
            'primary_cta_url' => route('products.index'),
            'secondary_cta_text' => 'CPNS/PPPK Starter Kit',
            'secondary_cta_url' => '#kategori-utama',
            'support_title' => 'Butuh bantuan memilih produk?',
            'support_text' => 'Tim Ruang Cerdas siap membantu Anda memilih produk digital edukasi yang paling sesuai dengan kebutuhan belajar dan kerja.',
            'support_whatsapp' => null,
            'featured_section_title' => 'Produk Digital Unggulan',
            'featured_section_subtitle' => 'Pilihan eBook, checklist, template, dan panduan praktis untuk belajar, kerja, dan persiapan seleksi.',
            'footer_short_text' => 'Ruang Cerdas - Produk digital praktis untuk belajar, kerja, dan seleksi ASN.',
            'seo_title' => 'Ruang Cerdas - Produk Digital Praktis untuk Belajar, Kerja, dan Seleksi ASN',
            'seo_description' => 'Ruang Cerdas menyediakan eBook, checklist, template, dan panduan siap pakai untuk CPNS/PPPK, ASN, administrasi kerja, skill digital pemula, dan produktivitas.',
            'seo_keywords' => null,
            'og_image_url' => null,
            'whatsapp_cta_text' => 'Hubungi WhatsApp Admin',
            'whatsapp_default_message' => null,
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
            'whatsapp_cta_text' => $landingSetting?->whatsapp_cta_text,
            'whatsapp_default_message' => $landingSetting?->whatsapp_default_message,
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

        $latestArticles = Article::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $leadMagnets = LeadMagnet::query()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        return view('public.home', compact('featuredProducts', 'landing', 'testimonials', 'latestArticles', 'leadMagnets'));
    }
}
