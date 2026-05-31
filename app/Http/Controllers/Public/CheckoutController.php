<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\LandingSetting;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\PricingService;
use App\Support\OrderAuditLogger;

class CheckoutController extends Controller
{
    public function create(Product $product, PricingService $pricingService)
    {
        abort_unless($product->isVisibleToPublic(), 404, 'Produk belum tersedia untuk dibeli.');

        $product->load('category');

        $pricing = $pricingService->resolve($product);

        return view('public.checkout.create', [
            'product' => $product,
            'pricing' => $pricing,
            'supportWhatsapp' => LandingSetting::query()->value('support_whatsapp'),
        ]);
    }

    public function store(
        CheckoutRequest $request,
        Product $product,
        CheckoutService $checkoutService
    ) {
        abort_unless($product->isVisibleToPublic(), 404, 'Produk belum tersedia untuk dibeli.');

        $order = $checkoutService->createOrder($product, $request->validated());

        OrderAuditLogger::log(
            $order,
            'order.created',
            'Order baru berhasil dibuat dari checkout.',
            [
                'invoice_number' => $order->invoice_number,
                'product_id' => $order->product_id,
                'product_name' => $product->name,
                'price' => (int) $order->price,
                'coupon_code' => $order->coupon_code,
                'discount_amount' => (float) ($order->discount_amount ?? 0),
            ],
            null,
            $order->status
        );

        return redirect()->route('orders.thank-you', $order->invoice_number);
    }
}
