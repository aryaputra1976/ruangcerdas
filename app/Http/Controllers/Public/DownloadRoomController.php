<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;
use App\Models\Order;
use App\Models\TryoutPackage;
use App\Services\SecureDownloadService;
use App\Support\ActivityLogger;
use App\Support\OrderAuditLogger;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DownloadRoomController extends Controller
{
    private const SESSION_KEY = 'download_room.access';

    public function index(): View
    {
        return view('public.download-room.index');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'buyer_email' => ['required', 'email', 'max:150'],
            'invoice_number' => ['required', 'string', 'max:100'],
        ]);

        $buyerEmail = mb_strtolower(trim($validated['buyer_email']));
        $invoiceNumber = trim($validated['invoice_number']);

        $order = Order::query()
            ->with('product.category')
            ->where('invoice_number', $invoiceNumber)
            ->whereRaw('LOWER(buyer_email) = ?', [$buyerEmail])
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->withErrors([
                    'download_room' => 'Data order tidak ditemukan. Pastikan email dan invoice sesuai.',
                ]);
        }

        $orders = Order::query()
            ->with('product.category')
            ->whereRaw('LOWER(buyer_email) = ?', [$buyerEmail])
            ->latest('created_at')
            ->latest('id')
            ->get();

        $this->storeAuthorizedAccess($request, $orders);

        $tryoutPackages = TryoutPackage::query()
            ->whereIn('slug', $orders->pluck('product.slug')->filter()->unique()->values())
            ->get()
            ->keyBy('slug');

        return view('public.download-room.show', [
            'buyerEmail' => $buyerEmail,
            'matchedInvoice' => $invoiceNumber,
            'orders' => $orders->map(fn (Order $listedOrder) => $this->buildOrderRow($listedOrder, $tryoutPackages, $invoiceNumber)),
        ]);
    }

    public function download(
        Request $request,
        Order $order,
        SecureDownloadService $secureDownloadService
    ) {
        abort_unless($this->hasAuthorizedAccess($request, $order), 403, 'Akses Ruang Akses tidak valid.');

        $secureDownloadService->validateDownload($order->loadMissing('product'), (string) $order->download_token);

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
            'order.downloaded_from_room',
            'Pembeli berhasil download produk dari Ruang Akses.',
            [
                'download_count' => (int) $order->download_count,
                'downloaded_at' => now()->toDateTimeString(),
                'invoice_number' => $order->invoice_number,
                'ip_address' => $request->ip(),
            ]
        );

        ActivityLogger::log(
            'order.download_room.downloaded',
            $order,
            'Pembeli berhasil download produk melalui Ruang Akses.',
            [
                'invoice_number' => $order->invoice_number,
                'product_id' => $order->product_id,
                'download_count' => (int) $order->download_count,
            ]
        );

        return Storage::disk('private')->download(
            $secureDownloadService->filePath($order),
            $secureDownloadService->downloadName($order)
        );
    }

    private function storeAuthorizedAccess(Request $request, Order|EloquentCollection $orders): void
    {
        $access = (array) $request->session()->get(self::SESSION_KEY, []);

        $items = $orders instanceof Order ? collect([$orders]) : $orders;

        foreach ($items as $order) {
            $access[$order->getKey()] = [
                'invoice_number' => (string) $order->invoice_number,
                'buyer_email' => mb_strtolower((string) $order->buyer_email),
                'granted_at' => now()->toDateTimeString(),
            ];
        }

        $request->session()->put(self::SESSION_KEY, $access);
    }

    private function buildOrderRow(Order $order, Collection $tryoutPackages, string $matchedInvoice): array
    {
        $statusLabel = $this->statusLabel($order);
        $statusClass = $this->statusClass($order);
        $isTryout = $order->product?->product_type === 'tryout';
        $tryoutPackage = $isTryout ? $tryoutPackages->get($order->product?->slug) : null;
        $downloadProblem = $isTryout ? null : $this->downloadProblem($order);
        $downloadAvailable = ! $isTryout && $downloadProblem === null;

        $actionLabel = null;
        $actionUrl = null;
        $actionClass = 'rc-btn-neutral';
        $actionNote = null;

        if ($isTryout) {
            if ($order->status === Order::STATUS_PAID && $tryoutPackage) {
                $actionLabel = 'Mulai Tryout';
                $actionUrl = route('public.tryouts.packages.start', [
                    'tryoutType' => $tryoutPackage->routeSegment(),
                    'tryoutPackage' => $tryoutPackage,
                ]);
                $actionClass = 'rc-btn-success';
                $actionNote = 'Gunakan email pembelian yang sama saat mulai.';
            } elseif (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_REJECTED], true)) {
                $actionLabel = 'Upload Bukti';
                $actionUrl = route('orders.payment.form', $order->invoice_number);
                $actionClass = 'rc-btn-primary';
            } elseif ($order->status === Order::STATUS_PAYMENT_UPLOADED) {
                $actionNote = 'Menunggu verifikasi admin.';
            } elseif ($order->status === Order::STATUS_EXPIRED) {
                $actionNote = 'Order tryout sudah kedaluwarsa.';
            } elseif ($order->status === Order::STATUS_PAID && ! $tryoutPackage) {
                $actionNote = 'Paket tryout tidak ditemukan.';
            }
        } else {
            if ($order->status === Order::STATUS_PAID && $downloadAvailable) {
                $actionLabel = 'Download';
                $actionUrl = route('public.download-room.download', $order);
                $actionClass = 'rc-btn-success';
            } elseif (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_REJECTED], true)) {
                $actionLabel = 'Upload Bukti';
                $actionUrl = route('orders.payment.form', $order->invoice_number);
                $actionClass = 'rc-btn-primary';
            } elseif ($order->status === Order::STATUS_PAYMENT_UPLOADED) {
                $actionNote = 'Menunggu verifikasi admin.';
            } elseif ($order->status === Order::STATUS_PAID) {
                $actionNote = $downloadProblem;
            } elseif ($order->status === Order::STATUS_EXPIRED) {
                $actionNote = 'Order sudah kedaluwarsa.';
            }
        }

        return [
            'order' => $order,
            'status_label' => $statusLabel,
            'status_class' => $statusClass,
            'is_tryout' => $isTryout,
            'type_label' => $isTryout ? 'Tryout' : 'Produk Digital',
            'matched_invoice' => $order->invoice_number === $matchedInvoice,
            'download_available' => $downloadAvailable,
            'download_problem' => $downloadProblem,
            'action_label' => $actionLabel,
            'action_url' => $actionUrl,
            'action_class' => $actionClass,
            'action_note' => $actionNote,
        ];
    }

    private function statusLabel(Order $order): string
    {
        return match ($order->status) {
            Order::STATUS_PENDING => 'Menunggu Pembayaran',
            Order::STATUS_PAYMENT_UPLOADED => 'Menunggu Verifikasi Admin',
            Order::STATUS_PAID => 'Pembayaran Disetujui',
            Order::STATUS_REJECTED => 'Pembayaran Ditolak',
            Order::STATUS_EXPIRED => 'Order Kedaluwarsa',
            default => ucfirst(str_replace('_', ' ', (string) $order->status)),
        };
    }

    private function statusClass(Order $order): string
    {
        return match ($order->status) {
            Order::STATUS_PENDING => 'bg-amber-100 text-amber-700',
            Order::STATUS_PAYMENT_UPLOADED => 'bg-blue-100 text-blue-700',
            Order::STATUS_PAID => 'bg-emerald-100 text-emerald-700',
            Order::STATUS_REJECTED => 'bg-red-100 text-red-700',
            Order::STATUS_EXPIRED => 'bg-slate-200 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    private function hasAuthorizedAccess(Request $request, Order $order): bool
    {
        $access = (array) $request->session()->get(self::SESSION_KEY, []);
        $orderAccess = $access[$order->getKey()] ?? null;

        if (! is_array($orderAccess)) {
            return false;
        }

        return ($orderAccess['invoice_number'] ?? null) === (string) $order->invoice_number
            && ($orderAccess['buyer_email'] ?? null) === mb_strtolower((string) $order->buyer_email);
    }

    private function downloadAvailable(Order $order): bool
    {
        return $this->downloadProblem($order) === null;
    }

    private function downloadProblem(Order $order): ?string
    {
        if ($order->status !== Order::STATUS_PAID) {
            return 'Download belum tersedia untuk status order ini.';
        }

        if (! $order->product) {
            return 'Produk tidak ditemukan.';
        }

        if (blank($order->product->digital_file_path)) {
            return 'File produk belum tersedia.';
        }

        if (! Storage::disk('private')->exists($order->product->digital_file_path)) {
            return 'File produk tidak ditemukan di storage private.';
        }

        $maxDownloadCount = (int) config('ruangcerdas.download.max_count', 5);
        if ($maxDownloadCount > 0 && (int) $order->download_count >= $maxDownloadCount) {
            return 'Batas maksimal download untuk order ini sudah tercapai.';
        }

        if ($order->download_expires_at && $order->download_expires_at->isPast()) {
            return 'Akses download untuk order ini sudah kedaluwarsa.';
        }

        if (blank($order->download_token)) {
            return 'Akses download belum siap. Silakan hubungi admin.';
        }

        return null;
    }
}
