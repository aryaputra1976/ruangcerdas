<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LandingSetting;
use App\Models\Product;
use App\Models\Testimonial;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function index(Request $request, PricingService $pricingService)
    {
        $publicVisibleProducts = Product::query()
            ->with('category')
            ->visibleToPublic()
            ->latest('published_at')
            ->get()
            ->filter(fn (Product $product) => $product->isVisibleToPublic())
            ->values();

        $query = Product::query()
            ->with('category')
            ->visibleToPublic()
            ->latest('published_at');

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($sub) use ($request) {
                $sub->where('slug', $request->category);
            });
        }

        $allProducts = $query->get()
            ->filter(fn (Product $product) => $product->isVisibleToPublic())
            ->values();

        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allProducts
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        $currentItems = $currentItems->map(function ($product) use ($pricingService) {
            $product->pricing = $pricingService->resolve($product);

            return $product;
        });

        $products = new LengthAwarePaginator(
            $currentItems,
            $allProducts->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $categories = Category::query()
            ->whereIn('id', $publicVisibleProducts->pluck('category_id')->filter()->unique()->values())
            ->orderBy('name')
            ->get();

        $landingSetting = LandingSetting::query()->first();
        $supportWhatsapp = $landingSetting?->support_whatsapp;

        return view('public.products.index', compact('products', 'categories', 'landingSetting', 'supportWhatsapp'));
    }

    public function show(Product $product, PricingService $pricingService)
    {
        abort_unless($product->isVisibleToPublic(), 404);

        $product->load([
            'category',
            'faqs' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id'),
        ]);

        $pricing = $pricingService->resolve($product);

        $testimonials = Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->take(3)
            ->get();

        $landingSetting = LandingSetting::query()->first();
        $supportWhatsapp = $landingSetting?->support_whatsapp;

        return view('public.products.show', compact('product', 'pricing', 'testimonials', 'supportWhatsapp', 'landingSetting'));
    }
}
