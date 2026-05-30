<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, PricingService $pricingService)
    {
        $query = Product::query()
            ->with('category')
            ->publicVisible()
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

        $products = $query->paginate(12)->withQueryString();

        $products->getCollection()->transform(function ($product) use ($pricingService) {
            $product->pricing = $pricingService->resolve($product);

            return $product;
        });

        $categories = Category::query()
            ->whereHas('products', function ($sub) {
                $sub->publicVisible();
            })
            ->orderBy('name')
            ->get();

        return view('public.products.index', compact('products', 'categories'));
    }

    public function show(Product $product, PricingService $pricingService)
    {
        abort_unless(
            $product->is_active && $product->published_at && $product->published_at->lte(now()),
            404
        );

        $product->load('category');

        $pricing = $pricingService->resolve($product);

        return view('public.products.show', compact('product', 'pricing'));
    }
}