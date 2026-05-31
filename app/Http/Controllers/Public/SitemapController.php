<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('products.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('public.faq'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('public.terms'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ],
            [
                'loc' => route('public.privacy'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ],
        ];

        $products = Product::query()
            ->visibleToPublic()
            ->latest('updated_at')
            ->get(['slug', 'updated_at', 'digital_file_path', 'is_active', 'published_at']);

        foreach ($products->filter(fn (Product $product) => $product->isVisibleToPublic()) as $product) {
            $urls[] = [
                'loc' => route('products.show', $product->slug),
                'lastmod' => $product->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        return response()
            ->view('public.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
