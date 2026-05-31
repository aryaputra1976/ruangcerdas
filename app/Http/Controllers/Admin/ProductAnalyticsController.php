<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
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
                'Paid Orders',
                'Paid Revenue',
                'Average Paid Order Value',
                'Downloads',
                'Pending Orders',
                'Rejected Orders',
                'Total Orders',
                'Conversion Rate',
                'Period From',
                'Period To',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['product']?->id,
                    $row['product']?->name ?? 'Produk tidak ditemukan',
                    $row['product']?->category?->name ?? '-',
                    $row['paid_orders'],
                    $row['paid_revenue'],
                    $row['average_order_value'],
                    $row['download_count'],
                    $row['pending_orders'],
                    $row['rejected_orders'],
                    $row['total_orders'],
                    rtrim(rtrim(number_format($row['conversion_rate'], 2, '.', ''), '0'), '.') . '%',
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

        $status = (string) $request->query('status', Order::STATUS_PAID);
        $productId = $request->query('product_id');
        $categoryId = $request->query('category_id');

        $baseOrdersQuery = Order::query()
            ->with('product.category')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', $status)
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('product', fn ($productQuery) => $productQuery->where('category_id', $categoryId));
            });

        $orders = (clone $baseOrdersQuery)->get();

        $rows = $orders
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()?->product;
                $paidOrders = $group->where('status', Order::STATUS_PAID);
                $pendingOrders = $group->where('status', Order::STATUS_PENDING);
                $rejectedOrders = $group->where('status', Order::STATUS_REJECTED);

                $paidCount = $paidOrders->count();
                $totalCount = $group->count();
                $paidRevenue = (int) $paidOrders->sum('price');
                $downloadCount = (int) $group->sum('download_count');

                return [
                    'product' => $product,
                    'paid_orders' => $paidCount,
                    'paid_revenue' => $paidRevenue,
                    'average_order_value' => $paidCount > 0 ? (int) round($paidRevenue / $paidCount) : 0,
                    'download_count' => $downloadCount,
                    'pending_orders' => $pendingOrders->count(),
                    'rejected_orders' => $rejectedOrders->count(),
                    'total_orders' => $totalCount,
                    'conversion_rate' => $totalCount > 0 ? round(($paidCount / $totalCount) * 100, 2) : 0,
                ];
            })
            ->sortBy([
                ['paid_orders', 'desc'],
                ['paid_revenue', 'desc'],
            ])
            ->values();

        $topProduct = $rows->first();
        $highestRevenue = (int) ($topProduct['paid_revenue'] ?? 0);

        $rows = $rows->map(function ($row) use ($highestRevenue) {
            $row['revenue_progress'] = $highestRevenue > 0
                ? round(($row['paid_revenue'] / $highestRevenue) * 100, 2)
                : 0;

            return $row;
        });

        $topProducts = $rows->take(10);

        $paidOrders = $orders->where('status', Order::STATUS_PAID);
        $totalPaidOrders = $paidOrders->count();
        $totalRevenuePaid = (int) $paidOrders->sum('price');

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
                'total_products_sold' => $rows->where('paid_orders', '>', 0)->count(),
                'total_paid_orders' => $totalPaidOrders,
                'total_revenue_paid' => $totalRevenuePaid,
                'average_paid_order_value' => $totalPaidOrders > 0 ? (int) round($totalRevenuePaid / $totalPaidOrders) : 0,
                'total_downloads' => (int) $orders->sum('download_count'),
                'best_seller' => $topProduct['product']?->name ?? '-',
            ],
        ];
    }
}
