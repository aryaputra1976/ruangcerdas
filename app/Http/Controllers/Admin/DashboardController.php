<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DownloadLog;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'missing_private_files_products' => Product::query()
                ->where('is_active', true)
                ->get(['id', 'digital_file_path'])
                ->filter(fn (Product $product) => $product->isMissingPrivateFile())
                ->count(),
            'published_products' => Product::where('is_active', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->count(),

            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),

            'total_orders' => Order::count(),
            'new_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'waiting_verification' => Order::where('status', Order::STATUS_PAYMENT_UPLOADED)->count(),
            'paid_orders' => Order::where('status', Order::STATUS_PAID)->count(),
            'rejected_orders' => Order::where('status', Order::STATUS_REJECTED)->count(),

            'revenue' => Order::where('status', Order::STATUS_PAID)->sum('price'),
            'today_revenue' => Order::where('status', Order::STATUS_PAID)
                ->where('paid_at', '>=', $today)
                ->sum('price'),
            'month_revenue' => Order::where('status', Order::STATUS_PAID)
                ->where('paid_at', '>=', $monthStart)
                ->sum('price'),

            'downloads' => Order::sum('download_count'),
            'download_logs' => class_exists(DownloadLog::class) ? DownloadLog::count() : 0,
        ];

        $latestOrders = Order::query()
            ->with('product')
            ->latest()
            ->take(8)
            ->get();

        $waitingOrders = Order::query()
            ->with('product')
            ->where('status', Order::STATUS_PAYMENT_UPLOADED)
            ->latest()
            ->take(6)
            ->get();

        $latestProducts = Product::query()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestOrders',
            'waitingOrders',
            'latestProducts'
        ));
    }
}
