<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class SecureDownloadService
{
    public function validateDownload(Order $order, string $token): void
    {
        abort_unless(
            $order->status === Order::STATUS_PAID,
            403,
            'Order belum dibayar atau belum di-approve.'
        );

        abort_unless(
            $order->download_token && hash_equals($order->download_token, $token),
            403,
            'Token download tidak valid.'
        );

        if ($order->download_expires_at && $order->download_expires_at->isPast()) {
            abort(403, 'Link download sudah expired.');
        }

        $maxDownloadCount = (int) config('ruangcerdas.download.max_count', 5);

        if ($maxDownloadCount > 0 && $order->download_count >= $maxDownloadCount) {
            abort(403, 'Batas maksimal download untuk order ini sudah tercapai.');
        }

        abort_unless($order->product, 404, 'Produk tidak ditemukan.');

        abort_unless($order->product->digital_file_path, 404, 'File produk belum tersedia.');

        abort_unless(
            Storage::disk('private')->exists($order->product->digital_file_path),
            404,
            'File produk tidak ditemukan di storage private.'
        );
    }

    public function filePath(Order $order): string
    {
        return $order->product->digital_file_path;
    }

    public function downloadName(Order $order): string
    {
        return $order->product->download_filename ?: basename($order->product->digital_file_path);
    }
}