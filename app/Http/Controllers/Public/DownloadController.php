<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;
use App\Models\Order;
use App\Services\SecureDownloadService;
use App\Support\OrderAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download(
        Request $request,
        string $invoice,
        string $token,
        SecureDownloadService $secureDownloadService
    ) {
        $order = Order::query()
            ->with('product')
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        $secureDownloadService->validateDownload($order, $token);

        $order->increment('download_count');

        DownloadLog::create([
            'order_id' => $order->id,
            'product_id' => $order->product_id,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'downloaded_at' => now(),
        ]);

        $order->refresh();

        OrderAuditLogger::log(
            $order,
            'order.downloaded',
            'Download file berhasil dilakukan.',
            [
                'download_count' => (int) $order->download_count,
                'downloaded_at' => now()->toDateTimeString(),
                'ip_address' => $request->ip(),
            ]
        );

        return Storage::disk('private')->download(
            $secureDownloadService->filePath($order),
            $secureDownloadService->downloadName($order)
        );
    }
}
