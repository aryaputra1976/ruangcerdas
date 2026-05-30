<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\PricingService;

class HomeController extends Controller
{
    public function index(PricingService $pricingService)
    {
        $featuredProducts = Product::query()
            ->with('category')
            ->publicVisible()
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(6)
            ->get();

        $featuredProducts->transform(function ($product) use ($pricingService) {
            $product->pricing = $pricingService->resolve($product);

            return $product;
        });

        return view('public.home', compact('featuredProducts'));
    }
}