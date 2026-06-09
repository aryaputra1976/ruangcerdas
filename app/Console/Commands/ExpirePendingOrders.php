<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Support\ActivityLogger;
use App\Support\OrderAuditLogger;
use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Expire pending orders older than 24 hours.';

    public function handle(): int
    {
        $expiredCount = 0;

        Order::query()
            ->where('status', Order::STATUS_PENDING)
            ->where('created_at', '<=', now()->subDay())
            ->orderBy('id')
            ->chunkById(100, function ($orders) use (&$expiredCount): void {
                foreach ($orders as $order) {
                    $fromStatus = $order->status;

                    $order->update([
                        'status' => Order::STATUS_EXPIRED,
                    ]);

                    OrderAuditLogger::log(
                        $order,
                        'order.expired',
                        'Sistem mengubah order pending lama menjadi expired.',
                        [
                            'invoice_number' => $order->invoice_number,
                            'trigger' => 'orders:expire-pending',
                        ],
                        $fromStatus,
                        $order->status
                    );

                    ActivityLogger::log(
                        'order.expired',
                        $order,
                        'Sistem mengubah order pending lama menjadi expired.',
                        [
                            'invoice_number' => $order->invoice_number,
                            'trigger' => 'orders:expire-pending',
                        ]
                    );

                    $expiredCount++;
                }
            });

        $this->info("Expired orders: {$expiredCount}");

        return self::SUCCESS;
    }
}
