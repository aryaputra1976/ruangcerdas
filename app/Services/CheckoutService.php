<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
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
            $originalPrice = (int) $this->pricingService->currentPrice($product);
            $discountAmount = 0;
            $finalPrice = $originalPrice;
            $coupon = null;

            if (!empty($data['coupon_code'])) {
                $coupon = $this->resolveCoupon($data['coupon_code'], $originalPrice);
                $discountAmount = $this->calculateDiscount($coupon, $originalPrice);
                $finalPrice = max(0, $originalPrice - $discountAmount);
            }

            $order = Order::create([
                'product_id' => $product->id,
                'coupon_id' => $coupon?->id,
                'invoice_number' => $this->orderNumberService->generate(),
                'buyer_name' => $data['buyer_name'],
                'buyer_email' => $data['buyer_email'],
                'buyer_whatsapp' => $data['buyer_whatsapp'],
                'price' => $finalPrice,
                'status' => Order::STATUS_PENDING,
                'payment_method' => 'manual',
                'coupon_code' => $coupon?->code,
                'discount_amount' => $discountAmount,
                'original_price' => $originalPrice,
                'download_count' => 0,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });
    }

    private function resolveCoupon(string $couponCode, int $price): Coupon
    {
        $code = strtoupper(trim($couponCode));

        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [$code])
            ->lockForUpdate()
            ->first();

        if (!$coupon || !$coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kode kupon tidak valid atau tidak aktif.',
            ]);
        }

        $now = now();

        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kode kupon belum bisa digunakan.',
            ]);
        }

        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kode kupon sudah kadaluarsa.',
            ]);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kuota penggunaan kupon sudah habis.',
            ]);
        }

        if ($coupon->min_order_amount !== null && $price < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kupon membutuhkan minimum belanja tertentu.',
            ]);
        }

        return $coupon;
    }

    private function calculateDiscount(Coupon $coupon, int $price): int
    {
        if ($coupon->type === Coupon::TYPE_FIXED) {
            return (int) round(min($price, (float) $coupon->value));
        }

        $discount = $price * ((float) $coupon->value / 100);

        if ($coupon->max_discount !== null) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        return (int) round(min($price, $discount));
    }
}
