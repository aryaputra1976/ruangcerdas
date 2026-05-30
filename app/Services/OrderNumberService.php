<?php

namespace App\Services;

use App\Models\Order;

class OrderNumberService
{
    public function generate(): string
    {
        $prefix = 'RC-' . now()->format('Ymd');

        $lastOrderToday = Order::query()
            ->where('invoice_number', 'like', $prefix . '-%')
            ->latest('id')
            ->first();

        if (! $lastOrderToday) {
            return $prefix . '-0001';
        }

        $lastNumber = (int) str()->afterLast($lastOrderToday->invoice_number, '-')->toString();
        $nextNumber = $lastNumber + 1;

        return $prefix . '-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}