<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderNumberService
{
    public function generate(): string
    {
        $prefix = 'RC-' . now()->format('Ymd');

        $lastOrderToday = Order::query()
            ->where('invoice_number', 'like', $prefix . '-%')
            ->orderByDesc('invoice_number')
            ->lockForUpdate()
            ->first();

        if (! $lastOrderToday) {
            return $prefix . '-0001';
        }

        $lastNumber = (int) Str::afterLast($lastOrderToday->invoice_number, '-');
        $nextNumber = $lastNumber + 1;

        return $prefix . '-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}