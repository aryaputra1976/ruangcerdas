<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderPaidMail;
use App\Models\Order;
use App\Models\OrderNote;
use App\Services\OrderBuyerMailService;
use App\Services\TryoutAccessService;
use App\Support\ActivityLogger;
use App\Support\OrderAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');

        $orders = Order::query()
            ->with(['product', 'notes'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('invoice_number', 'like', "%{$q}%")
                        ->orWhere('buyer_name', 'like', "%{$q}%")
                        ->orWhere('buyer_email', 'like', "%{$q}%")
                        ->orWhere('buyer_whatsapp', 'like', "%{$q}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($from, function ($query) use ($from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('status', Order::STATUS_PENDING)->count(),
            'payment_uploaded' => Order::where('status', Order::STATUS_PAYMENT_UPLOADED)->count(),
            'paid' => Order::where('status', Order::STATUS_PAID)->count(),
            'rejected' => Order::where('status', Order::STATUS_REJECTED)->count(),
            'expired' => Order::where('status', Order::STATUS_EXPIRED)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'q', 'status', 'from', 'to'));
    }

    public function show(Order $order)
    {
        $order->load([
            'product.category',
            'approver',
            'notes' => fn ($query) => $query
                ->with('user')
                ->orderByDesc('is_pinned')
                ->latest('created_at'),
            'auditTrails' => fn ($query) => $query->latest('created_at'),
            'auditTrails.user',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function paymentProof(Order $order): BinaryFileResponse
    {
        abort_if(blank($order->payment_proof_path), 404);

        $relativePath = ltrim(str_replace('\\', '/', (string) $order->payment_proof_path), '/');
        abort_if(
            str_contains($relativePath, '../') || str_contains($relativePath, '..\\'),
            404
        );

        foreach (['private', 'public'] as $diskName) {
            if (! Storage::disk($diskName)->exists($relativePath)) {
                continue;
            }

            $basePath = storage_path($diskName === 'private' ? 'app/private' : 'app/public');
            $absolutePath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $realBasePath = realpath($basePath);
            $realAbsolutePath = realpath($absolutePath);

            if (
                $realBasePath === false
                || $realAbsolutePath === false
                || ! str_starts_with($realAbsolutePath, $realBasePath . DIRECTORY_SEPARATOR)
                || ! is_file($realAbsolutePath)
            ) {
                continue;
            }

            $mimeType = mime_content_type($realAbsolutePath) ?: 'application/octet-stream';
            $safeFilename = basename($realAbsolutePath);

            if ($mimeType === 'application/pdf') {
                return response()->download(
                    $realAbsolutePath,
                    $safeFilename,
                    [
                        'Content-Type' => 'application/pdf',
                        'X-Content-Type-Options' => 'nosniff',
                        'Content-Security-Policy' => "default-src 'none'; sandbox",
                    ],
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT
                );
            }

            return response()->file($realAbsolutePath, [
                'Content-Type' => $mimeType,
                'X-Content-Type-Options' => 'nosniff',
                'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; style-src 'none'; sandbox",
            ]);
        }

        abort(404);
    }

    public function approve(
        Request $request,
        Order $order,
        TryoutAccessService $tryoutAccessService,
        OrderBuyerMailService $orderBuyerMailService
    )
    {
        abort_if($order->isPaid(), 422, 'Order sudah paid.');

        $fromStatus = $order->status;
        $hadDownloadToken = filled($order->download_token);
        $couponUsageIncremented = false;

        $order = DB::transaction(function () use ($request, $order, $tryoutAccessService, $hadDownloadToken, $fromStatus, &$couponUsageIncremented) {
            $order->update([
                'status' => Order::STATUS_PAID,
                'paid_at' => now(),
                'approved_at' => now(),
                'approved_by' => $request->user()->id,
                'download_token' => $order->download_token ?: Str::random(64),
                'download_expires_at' => now()->addDays(config('ruangcerdas.download.expire_days', 7)),
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            if ($order->coupon_id) {
                $order->coupon()->increment('used_count');
                $couponUsageIncremented = true;
            }

            $order = $order->fresh(['product', 'coupon']);

            $tryoutAccessService->ensureAccessFromPaidOrder($order);

            OrderAuditLogger::log(
                $order,
                'order.approved',
                'Admin menyetujui pembayaran order.',
                [
                    'invoice_number' => $order->invoice_number,
                    'admin_id' => $request->user()->id,
                    'coupon_usage_incremented' => $couponUsageIncremented,
                ],
                $fromStatus,
                $order->status
            );

            if ($couponUsageIncremented) {
                OrderAuditLogger::log(
                    $order,
                    'coupon.redeemed',
                    'Sistem mencatat penggunaan kupon saat order menjadi paid.',
                    [
                        'invoice_number' => $order->invoice_number,
                        'coupon_code' => $order->coupon_code,
                    ],
                    null,
                    null
                );
            }

            if (! $hadDownloadToken && filled($order->download_token)) {
                OrderAuditLogger::log(
                    $order,
                    'download_link.generated',
                    'Sistem membuat token download setelah order paid.',
                    [
                        'invoice_number' => $order->invoice_number,
                    ],
                    null,
                    null
                );
            }

            return $order;
        });

        $successMessage = 'Pembayaran berhasil di-approve. Link download sudah aktif.';

        if ($order->product?->product_type === 'tryout') {
            $successMessage = 'Pembayaran berhasil di-approve. Akses tryout premium sudah aktif untuk email pembeli.';
        }

        $orderBuyerMailService->sendOrderPaid($order->fresh('product'));

        ActivityLogger::log(
            'order.approved',
            $order,
            'Admin menyetujui pembayaran order.',
            [
                'invoice_number' => $order->invoice_number,
                'coupon_usage_incremented' => $couponUsageIncremented,
            ]
        );

        if ($couponUsageIncremented) {
            ActivityLogger::log(
                'coupon.redeemed',
                $order,
                'Sistem mencatat penggunaan kupon saat order menjadi paid.',
                [
                    'invoice_number' => $order->invoice_number,
                    'coupon_code' => $order->coupon_code,
                ]
            );
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', $successMessage);
    }

    public function reject(Request $request, Order $order, OrderBuyerMailService $orderBuyerMailService)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        abort_if($order->isPaid(), 422, 'Order yang sudah paid tidak bisa ditolak.');

        $fromStatus = $order->status;

        $order->update([
            'status' => Order::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        OrderAuditLogger::log(
            $order,
            'order.rejected',
            'Admin menolak pembayaran order.',
            [
                'invoice_number' => $order->invoice_number,
                'admin_id' => $request->user()->id,
                'rejection_reason' => $order->rejection_reason,
            ],
            $fromStatus,
            $order->status
        );

        ActivityLogger::log(
            'order.rejected',
            $order,
            'Admin menolak pembayaran order.',
            ['invoice_number' => $order->invoice_number]
        );

        $orderBuyerMailService->sendOrderRejected($order->fresh('product'));

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order berhasil ditolak.');
    }

    public function destroy(Request $request, Order $order)
    {
        if ($order->isProtectedFromAdminDeletion()) {
            return redirect()
                ->back()
                ->with('error', 'Order paid atau menunggu verifikasi pembayaran tidak dapat dihapus.');
        }

        if ($order->isPending()) {
            if (! $order->shouldBeExpiredBeforeDelete()) {
                return redirect()
                    ->back()
                    ->with('error', 'Order pending yang belum berumur 24 jam tidak dapat dihapus.');
            }

            $fromStatus = $order->status;

            $order->update([
                'status' => Order::STATUS_EXPIRED,
            ]);

            OrderAuditLogger::log(
                $order,
                'order.expired',
                'Admin menandai order pending lama sebagai expired sebelum penghapusan.',
                [
                    'invoice_number' => $order->invoice_number,
                    'admin_id' => $request->user()->id,
                ],
                $fromStatus,
                $order->status
            );

            ActivityLogger::log(
                'order.expired',
                $order,
                'Admin menandai order pending lama sebagai expired sebelum penghapusan.',
                ['invoice_number' => $order->invoice_number]
            );

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Order pending lama diubah menjadi expired terlebih dahulu. Tinjau kembali lalu hapus jika masih diperlukan.');
        }

        if (! $order->canBeSoftDeletedByAdmin()) {
            return redirect()
                ->back()
                ->with('error', 'Status order ini tidak dapat dihapus.');
        }

        $statusBeforeDelete = $order->status;
        $invoiceNumber = $order->invoice_number;

        $order->delete();

        OrderAuditLogger::log(
            $order,
            'order.deleted',
            'Admin menyembunyikan order dari daftar admin melalui soft delete.',
            [
                'invoice_number' => $invoiceNumber,
                'admin_id' => $request->user()->id,
            ],
            $statusBeforeDelete,
            $statusBeforeDelete
        );

        ActivityLogger::log(
            'order.deleted',
            $order,
            'Admin menyembunyikan order dari daftar admin melalui soft delete.',
            ['invoice_number' => $invoiceNumber]
        );

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order berhasil disembunyikan dari daftar admin.');
    }

    public function resendDownloadLink(Request $request, Order $order)
    {
        $order->loadMissing('product');

        if ($order->status !== Order::STATUS_PAID
            || blank($order->buyer_email)
            || ! $order->product
            || ! $order->product->privateFileExists()) {
            return redirect()
                ->back()
                ->with('error', 'Link download belum dapat dikirim ulang karena order belum paid / email tidak tersedia / file produk belum tersedia.');
        }

        $regeneratedToken = blank($order->download_token)
            || blank($order->download_expires_at)
            || $order->download_expires_at->isPast();

        if ($regeneratedToken) {
            $order->forceFill([
                'download_token' => Str::random(64),
                'download_expires_at' => now()->addDays(config('ruangcerdas.download.expire_days', 7)),
            ])->save();
        }

        try {
            Mail::to($order->buyer_email)->send(new OrderPaidMail($order->fresh('product')));
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim ulang email link download.', [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'buyer_email' => $order->buyer_email,
                'error' => $exception->getMessage(),
            ]);

            $errorMessage = 'Gagal mengirim ulang email link download. Periksa konfigurasi mail server (SMTP) dan kredensial akun email.';

            if (str_contains(strtolower($exception->getMessage()), 'authenticate')) {
                $errorMessage = 'Gagal autentikasi SMTP. Periksa MAIL_USERNAME dan MAIL_PASSWORD pada file .env.';
            }

            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }

        ActivityLogger::log(
            'order.download_link_resent',
            $order,
            'Admin mengirim ulang link download ke email pembeli.',
            [
                'invoice_number' => $order->invoice_number,
                'buyer_email' => $order->buyer_email,
                'regenerated_token' => $regeneratedToken,
            ]
        );

        OrderAuditLogger::log(
            $order,
            'download_link.resent',
            'Admin mengirim ulang link download ke email pembeli.',
            [
                'invoice_number' => $order->invoice_number,
                'buyer_email' => $order->buyer_email,
                'regenerated_token' => $regeneratedToken,
            ],
            $order->status,
            $order->status
        );

        return redirect()
            ->back()
            ->with('success', 'Link download berhasil dikirim ulang ke email pembeli.');
    }

    public function storeNote(Request $request, Order $order)
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $note = $order->notes()->create([
            'user_id' => $request->user()->id,
            'note' => trim($validated['note']),
            'is_pinned' => (bool) ($validated['is_pinned'] ?? false),
        ]);

        ActivityLogger::log(
            'order_note.created',
            $order,
            'Admin menambahkan catatan internal order.',
            [
                'invoice_number' => $order->invoice_number,
                'note_id' => $note->id,
                'is_pinned' => $note->is_pinned,
            ]
        );

        OrderAuditLogger::log(
            $order,
            'note.created',
            'Admin menambahkan catatan internal.',
            [
                'invoice_number' => $order->invoice_number,
                'note_id' => $note->id,
                'is_pinned' => $note->is_pinned,
                'note_preview' => str($note->note)->limit(80, ''),
            ],
            $order->status,
            $order->status
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Catatan internal berhasil ditambahkan.');
    }

    public function updateNote(Request $request, Order $order, OrderNote $note)
    {
        if ($note->order_id !== $order->id) {
            abort(404);
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $note->update([
            'note' => trim($validated['note']),
            'is_pinned' => (bool) ($validated['is_pinned'] ?? false),
        ]);

        ActivityLogger::log(
            'order_note.updated',
            $order,
            'Admin memperbarui catatan internal order.',
            [
                'invoice_number' => $order->invoice_number,
                'note_id' => $note->id,
                'is_pinned' => $note->is_pinned,
            ]
        );

        OrderAuditLogger::log(
            $order,
            'note.updated',
            'Admin memperbarui catatan internal.',
            [
                'invoice_number' => $order->invoice_number,
                'note_id' => $note->id,
                'is_pinned' => $note->is_pinned,
                'note_preview' => str($note->note)->limit(80, ''),
            ],
            $order->status,
            $order->status
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Catatan internal berhasil diperbarui.');
    }

    public function destroyNote(Order $order, OrderNote $note)
    {
        if ($note->order_id !== $order->id) {
            abort(404);
        }

        $noteId = $note->id;
        $isPinned = $note->is_pinned;
        $notePreview = str($note->note)->limit(80, '');

        $note->delete();

        ActivityLogger::log(
            'order_note.deleted',
            $order,
            'Admin menghapus catatan internal order.',
            [
                'invoice_number' => $order->invoice_number,
                'note_id' => $noteId,
                'is_pinned' => $isPinned,
            ]
        );

        OrderAuditLogger::log(
            $order,
            'note.deleted',
            'Admin menghapus catatan internal.',
            [
                'invoice_number' => $order->invoice_number,
                'note_id' => $noteId,
                'is_pinned' => $isPinned,
                'note_preview' => $notePreview,
            ],
            $order->status,
            $order->status
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Catatan internal berhasil dihapus.');
    }
}
