<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\PricingService;

class CheckoutController extends Controller
{
    public function create(Product $product, PricingService $pricingService)
    {
        abort_unless(
            $product->is_active && $product->published_at && $product->published_at->lte(now()),
            404
        );

        $product->load('category');

        $pricing = $pricingService->resolve($product);

        return view('public.checkout.create', [
            'product' => $product,
            'pricing' => $pricing,
        ]);
    }

    public function store(
        CheckoutRequest $request,
        Product $product,
        CheckoutService $checkoutService
    ) {
        abort_unless(
            $product->is_active && $product->published_at && $product->published_at->lte(now()),
            404
        );

        $order = $checkoutService->createOrder($product, $request->validated());

        return redirect()->route('orders.thank-you', $order->invoice_number);
    }
}