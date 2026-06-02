<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\LeadMagnet;
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
            [
                'loc' => route('articles.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('lead-magnets.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
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

        $articles = Article::query()
            ->published()
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);

        foreach ($articles as $article) {
            $urls[] = [
                'loc' => route('articles.show', $article->slug),
                'lastmod' => $article->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        $leadMagnets = LeadMagnet::query()
            ->where('is_active', true)
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);

        foreach ($leadMagnets as $leadMagnet) {
            $urls[] = [
                'loc' => route('lead-magnets.show', $leadMagnet->slug),
                'lastmod' => $leadMagnet->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        return response()
            ->view('public.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
