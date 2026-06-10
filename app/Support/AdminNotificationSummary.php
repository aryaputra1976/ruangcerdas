<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminNotificationSummary
{
    public static function make(): array
    {
        try {
            $pendingOrdersCount = Order::query()
                ->where('status', Order::STATUS_PENDING)
                ->count();

            $waitingVerificationCount = Order::query()
                ->where('status', Order::STATUS_PAYMENT_UPLOADED)
                ->count();

            $paidOrdersTodayCount = Order::query()
                ->where('status', Order::STATUS_PAID)
                ->whereDate('paid_at', today())
                ->count();

            $rejectedOrdersTodayCount = Order::query()
                ->where('status', Order::STATUS_REJECTED)
                ->whereDate('rejected_at', today())
                ->count();

            $newOrdersTodayCount = Order::query()
                ->whereDate('created_at', today())
                ->count();

            $missingProductFilesCount = Product::query()
                ->where('is_active', true)
                ->get(['id', 'digital_file_path'])
                ->filter(fn (Product $product) => $product->isMissingPrivateFile())
                ->count();

            return [
                'pending_orders_count' => $pendingOrdersCount,
                'waiting_verification_count' => $waitingVerificationCount,
                'paid_orders_today_count' => $paidOrdersTodayCount,
                'rejected_orders_today_count' => $rejectedOrdersTodayCount,
                'new_orders_today_count' => $newOrdersTodayCount,
                'missing_product_files_count' => $missingProductFilesCount,
                'total_attention_count' => $pendingOrdersCount + $waitingVerificationCount,
            ];
        } catch (Throwable $exception) {
            Log::warning('Gagal memuat ringkasan notifikasi admin.', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'pending_orders_count' => 0,
                'waiting_verification_count' => 0,
                'paid_orders_today_count' => 0,
                'rejected_orders_today_count' => 0,
                'new_orders_today_count' => 0,
                'missing_product_files_count' => 0,
                'total_attention_count' => 0,
            ];
        }
    }
}
