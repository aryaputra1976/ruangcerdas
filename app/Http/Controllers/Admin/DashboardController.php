<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DownloadLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\TryoutPackage;
use App\Models\TryoutSession;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'archived_products' => Product::onlyTrashed()->count(),
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
            'today_orders' => Order::where('created_at', '>=', $today)->count(),
            'new_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'waiting_verification' => Order::where('status', Order::STATUS_PAYMENT_UPLOADED)->count(),
            'paid_orders' => Order::where('status', Order::STATUS_PAID)->count(),
            'paid_orders_this_month' => Order::where('status', Order::STATUS_PAID)
                ->where('paid_at', '>=', $monthStart)
                ->count(),
            'rejected_orders' => Order::where('status', Order::STATUS_REJECTED)->count(),
            'expired_orders' => Order::where('status', Order::STATUS_EXPIRED)->count(),

            'revenue' => Order::where('status', Order::STATUS_PAID)->sum('price'),
            'today_revenue' => Order::where('status', Order::STATUS_PAID)
                ->where('paid_at', '>=', $today)
                ->sum('price'),
            'month_revenue' => Order::where('status', Order::STATUS_PAID)
                ->where('paid_at', '>=', $monthStart)
                ->sum('price'),

            'downloads' => Order::sum('download_count'),
            'download_logs' => class_exists(DownloadLog::class) ? DownloadLog::count() : 0,
            'total_tryout_packages' => TryoutPackage::count(),
            'active_tryout_packages' => TryoutPackage::where('is_active', true)->count(),
            'total_tryout_sessions' => TryoutSession::count(),
            'ongoing_tryout_sessions' => TryoutSession::where('status', TryoutSession::STATUS_ONGOING)->count(),
            'finished_tryout_sessions' => TryoutSession::where('status', TryoutSession::STATUS_FINISHED)->count(),
        ];

        $latestOrders = Order::query()
            ->with('product')
            ->latest()
            ->take(5)
            ->get();

        $waitingOrders = Order::query()
            ->with('product')
            ->where('status', Order::STATUS_PAYMENT_UPLOADED)
            ->latest()
            ->take(5)
            ->get();

        $topProducts = Product::withTrashed()
            ->with('category')
            ->whereHas('orders', fn ($query) => $query->where('status', Order::STATUS_PAID))
            ->withCount([
                'orders as paid_orders_count' => fn ($query) => $query->where('status', Order::STATUS_PAID),
            ])
            ->withSum([
                'orders as paid_orders_revenue' => fn ($query) => $query->where('status', Order::STATUS_PAID),
            ], 'price')
            ->orderByDesc('paid_orders_count')
            ->orderByDesc(DB::raw('COALESCE(paid_orders_revenue, 0)'))
            ->take(5)
            ->get();

        $latestTryoutSessions = TryoutSession::query()
            ->with('package')
            ->withCount('answers')
            ->latest()
            ->take(5)
            ->get();

        $topTryoutPackages = TryoutPackage::query()
            ->withCount('sessions')
            ->withCount([
                'sessions as finished_sessions_count' => fn ($query) => $query->where('status', TryoutSession::STATUS_FINISHED),
            ])
            ->withAvg([
                'sessions as finished_sessions_avg_score' => fn ($query) => $query->where('status', TryoutSession::STATUS_FINISHED),
            ], 'total_score')
            ->orderByDesc('sessions_count')
            ->orderByDesc('finished_sessions_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestOrders',
            'waitingOrders',
            'topProducts',
            'latestTryoutSessions',
            'topTryoutPackages'
        ));
    }
}
