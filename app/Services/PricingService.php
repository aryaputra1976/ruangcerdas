<?php

namespace App\Services;

use App\Models\Product;

class PricingService
{
    public function resolve(Product $product): array
    {
        $paidOrdersCount = $this->paidOrdersCount($product);

        $normalPrice = (int) ($product->normal_price ?? 0);
        $salePrice = (int) ($product->sale_price ?? 0);
        $firstBuyerPrice = (int) ($product->first_buyer_price ?? 0);
        $firstBuyerQuota = (int) ($product->first_buyer_quota ?? 0);

        $remainingQuota = max($firstBuyerQuota - $paidOrdersCount, 0);

        if ($firstBuyerPrice > 0 && $remainingQuota > 0) {
            return [
                'price' => $firstBuyerPrice,
                'normal_price' => $normalPrice,
                'sale_price' => $salePrice > 0 ? $salePrice : null,
                'label' => 'Harga Pembeli Pertama',
                'is_discounted' => $normalPrice > 0 && $firstBuyerPrice < $normalPrice,
                'remaining_quota' => $remainingQuota,
                'is_first_buyer' => true,
            ];
        }

        if ($salePrice > 0) {
            return [
                'price' => $salePrice,
                'normal_price' => $normalPrice,
                'sale_price' => $salePrice,
                'label' => 'Harga Promo',
                'is_discounted' => $normalPrice > 0 && $salePrice < $normalPrice,
                'remaining_quota' => 0,
                'is_first_buyer' => false,
            ];
        }

        return [
            'price' => $normalPrice,
            'normal_price' => $normalPrice,
            'sale_price' => null,
            'label' => 'Harga Normal',
            'is_discounted' => false,
            'remaining_quota' => 0,
            'is_first_buyer' => false,
        ];
    }

    public function currentPrice(Product $product): int
    {
        return (int) $this->resolve($product)['price'];
    }

    public function isFirstBuyerPriceActive(Product $product): bool
    {
        return (bool) $this->resolve($product)['is_first_buyer'];
    }

    public function remainingFirstBuyerQuota(Product $product): int
    {
        return (int) $this->resolve($product)['remaining_quota'];
    }

    private function paidOrdersCount(Product $product): int
    {
        return $product->orders()
            ->where('status', 'paid')
            ->count();
    }
}