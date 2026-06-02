<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductViewEvent;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $analyticsData = $this->getAnalyticsData($request);

        $products = Product::query()->orderBy('name')->get(['id', 'name']);
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.analytics.products', [
            'filters' => $analyticsData['filters'],
            'statusOptions' => [
                '',
                Order::STATUS_PAID,
                Order::STATUS_PENDING,
                Order::STATUS_PAYMENT_UPLOADED,
                Order::STATUS_REJECTED,
                Order::STATUS_CANCELLED,
                Order::STATUS_EXPIRED,
            ],
            'summary' => $analyticsData['summary'],
            'rows' => $analyticsData['rows'],
            'topProducts' => $analyticsData['topProducts'],
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $analyticsData = $this->getAnalyticsData($request);
        $filters = $analyticsData['filters'];
        $rows = $analyticsData['rows'];

        ActivityLogger::log(
            'analytics.products_exported',
            null,
            'Admin mengekspor analytics produk.',
            [
                'from' => $filters['from'],
                'to' => $filters['to'],
                'product_id' => $filters['product_id'],
                'category_id' => $filters['category_id'],
                'status' => $filters['status'],
            ]
        );

        $filename = 'ruang-cerdas-product-analytics-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows, $filters): void {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Product ID',
                'Product Name',
                'Category',
                'Views',
                'Checkout Started',
                'Payment Proof Uploaded',
                'Paid Orders',
                'Total Orders',
                'Order per View Conversion',
                'Paid per View Conversion',
                'Period From',
                'Period To',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['product']?->id,
                    $row['product']?->name ?? 'Produk tidak ditemukan',
                    $row['product']?->category?->name ?? '-',
                    $row['total_views'],
                    $row['checkout_started'],
                    $row['payment_uploaded_orders'],
                    $row['paid_orders'],
                    $row['total_orders'],
                    rtrim(rtrim(number_format($row['conversion_order_views'], 2, '.', ''), '0'), '.') . '%',
                    rtrim(rtrim(number_format($row['conversion_paid_views'], 2, '.', ''), '0'), '.') . '%',
                    $filters['from'],
                    $filters['to'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function getAnalyticsData(Request $request): array
    {
        $defaultFrom = now()->subDays(29)->toDateString();
        $defaultTo = now()->toDateString();

        $fromInput = $request->query('from', $defaultFrom);
        $toInput = $request->query('to', $defaultTo);

        try {
            $from = Carbon::parse($fromInput)->startOfDay();
        } catch (\Throwable $exception) {
            $from = Carbon::parse($defaultFrom)->startOfDay();
        }

        try {
            $to = Carbon::parse($toInput)->endOfDay();
        } catch (\Throwable $exception) {
            $to = Carbon::parse($defaultTo)->endOfDay();
        }

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $status = (string) $request->query('status', '');
        $productId = $request->query('product_id');
        $categoryId = $request->query('category_id');

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('product', fn ($productQuery) => $productQuery->where('category_id', $categoryId));
            });

        $orders = (clone $ordersQuery)->get();

        $viewEvents = ProductViewEvent::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('product', fn ($productQuery) => $productQuery->where('category_id', $categoryId));
            })
            ->get();

        $orderGroups = $orders->groupBy('product_id');
        $viewGroups = $viewEvents->groupBy('product_id');
        $productIds = $orderGroups->keys()->merge($viewGroups->keys())->filter()->unique()->values();

        $productsById = Product::query()
            ->with('category')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $rows = $productIds->map(function ($pid) use ($orderGroups, $viewGroups, $productsById) {
            $orderGroup = $orderGroups->get($pid, collect());
            $viewGroup = $viewGroups->get($pid, collect());

            $totalViews = $viewGroup->count();
            $totalOrders = $orderGroup->count();
            $paidOrders = $orderGroup->where('status', Order::STATUS_PAID)->count();
            $paymentUploadedOrders = $orderGroup->where('status', Order::STATUS_PAYMENT_UPLOADED)->count();

            return [
                'product' => $productsById->get($pid),
                'total_views' => $totalViews,
                'checkout_started' => $totalOrders,
                'total_orders' => $totalOrders,
                'payment_uploaded_orders' => $paymentUploadedOrders,
                'paid_orders' => $paidOrders,
                'conversion_order_views' => $totalViews > 0 ? round(($totalOrders / $totalViews) * 100, 2) : 0,
                'conversion_paid_views' => $totalViews > 0 ? round(($paidOrders / $totalViews) * 100, 2) : 0,
            ];
        })
            ->sortByDesc('total_views')
            ->values();

        $topProduct = $rows->sortByDesc('paid_orders')->first();

        $topProducts = $rows->take(10);
        $totalViews = (int) $viewEvents->count();
        $totalOrders = (int) $orders->count();
        $totalPaymentUploaded = (int) $orders->where('status', Order::STATUS_PAYMENT_UPLOADED)->count();
        $totalPaidOrders = (int) $orders->where('status', Order::STATUS_PAID)->count();

        return [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'status' => $status,
                'product_id' => $productId,
                'category_id' => $categoryId,
            ],
            'rows' => $rows,
            'topProducts' => $topProducts,
            'summary' => [
                'total_products_with_activity' => $rows->count(),
                'total_views' => $totalViews,
                'total_checkout_started' => $totalOrders,
                'total_orders' => $totalOrders,
                'total_payment_uploaded' => $totalPaymentUploaded,
                'total_paid_orders' => $totalPaidOrders,
                'conversion_order_views' => $totalViews > 0 ? round(($totalOrders / $totalViews) * 100, 2) : 0,
                'conversion_paid_views' => $totalViews > 0 ? round(($totalPaidOrders / $totalViews) * 100, 2) : 0,
                'best_converter' => data_get($topProduct, 'product.name', '-'),
            ],
        ];
    }
}
