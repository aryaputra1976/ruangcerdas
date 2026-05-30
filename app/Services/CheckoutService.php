<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        protected PricingService $pricingService,
        protected OrderNumberService $orderNumberService,
    ) {
    }

    public function createOrder(Product $product, array $data): Order
    {
        return DB::transaction(function () use ($product, $data) {
            $price = $this->pricingService->currentPrice($product);

            return Order::create([
                'product_id' => $product->id,
                'invoice_number' => $this->orderNumberService->generate(),
                'buyer_name' => $data['buyer_name'],
                'buyer_email' => $data['buyer_email'],
                'buyer_whatsapp' => $data['buyer_whatsapp'],
                'price' => $price,
                'status' => Order::STATUS_PENDING,
                'payment_method' => 'manual',
                'download_count' => 0,
            ]);
        });
    }
}