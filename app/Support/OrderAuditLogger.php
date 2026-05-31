<?php

namespace App\Support;

use App\Models\Order;
use App\Models\OrderAuditTrail;

class OrderAuditLogger
{
    public static function log(
        Order $order,
        string $action,
        ?string $description = null,
        array $properties = [],
        ?string $fromStatus = null,
        ?string $toStatus = null
    ): void {
        try {
            OrderAuditTrail::query()->create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'action' => $action,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'description' => $description,
                'properties' => $properties,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
