<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'status' => $request->query('status'),
            'product_id' => $request->query('product_id'),
        ];

        $applyBaseFilters = function ($query) use ($filters) {
            return $query
                ->when($filters['from'], fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
                ->when($filters['to'], fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
                ->when($filters['product_id'], fn ($q, $productId) => $q->where('product_id', $productId));
        };

        $orders = Order::query()
            ->with('product')
            ->tap($applyBaseFilters)
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total_order' => Order::query()->tap($applyBaseFilters)->count(),
            'total_paid' => Order::query()->tap($applyBaseFilters)->where('status', Order::STATUS_PAID)->count(),
            'total_pending' => Order::query()->tap($applyBaseFilters)->where('status', Order::STATUS_PENDING)->count(),
            'total_waiting_verification' => Order::query()->tap($applyBaseFilters)->where('status', Order::STATUS_PAYMENT_UPLOADED)->count(),
            'total_rejected' => Order::query()->tap($applyBaseFilters)->where('status', Order::STATUS_REJECTED)->count(),
            'total_revenue_paid' => (int) Order::query()
                ->tap($applyBaseFilters)
                ->where('status', Order::STATUS_PAID)
                ->sum('price'),
            'total_download' => (int) Order::query()
                ->tap($applyBaseFilters)
                ->sum('download_count'),
        ];

        $topProducts = Order::query()
            ->select('product_id')
            ->selectRaw('COUNT(*) as paid_orders_count')
            ->selectRaw('SUM(price) as total_revenue')
            ->where('status', Order::STATUS_PAID)
            ->whereNotNull('product_id')
            ->tap($applyBaseFilters)
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('paid_orders_count')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $statusOptions = [
            Order::STATUS_PENDING,
            Order::STATUS_PAYMENT_UPLOADED,
            Order::STATUS_PAID,
            Order::STATUS_REJECTED,
            Order::STATUS_CANCELLED,
            Order::STATUS_EXPIRED,
        ];

        return view('admin.reports.index', compact(
            'orders',
            'products',
            'filters',
            'statusOptions',
            'summary',
            'topProducts'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = [
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'status' => $request->query('status'),
            'product_id' => $request->query('product_id'),
        ];

        $applyBaseFilters = function ($query) use ($filters) {
            return $query
                ->when($filters['from'], fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
                ->when($filters['to'], fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
                ->when($filters['product_id'], fn ($q, $productId) => $q->where('product_id', $productId));
        };

        $orders = Order::query()
            ->with('product')
            ->tap($applyBaseFilters)
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->get();

        $filename = 'ruang-cerdas-orders-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'invoice',
                'buyer_name',
                'buyer_email',
                'buyer_whatsapp',
                'product_name',
                'price',
                'status',
                'created_at',
                'paid_at',
                'download_count',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->invoice_number,
                    $order->buyer_name,
                    $order->buyer_email,
                    $order->buyer_whatsapp,
                    $order->product?->name,
                    (int) ($order->price ?? 0),
                    $order->status,
                    $order->created_at?->format('Y-m-d H:i:s'),
                    $order->paid_at?->format('Y-m-d H:i:s'),
                    (int) ($order->download_count ?? 0),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
