<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'new_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'waiting_verification' => Order::where('status', Order::STATUS_PAYMENT_UPLOADED)->count(),
            'paid_orders' => Order::where('status', Order::STATUS_PAID)->count(),
            'rejected_orders' => Order::where('status', Order::STATUS_REJECTED)->count(),
            'revenue' => Order::where('status', Order::STATUS_PAID)->sum('price'),
            'downloads' => Order::sum('download_count'),
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
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestOrders',
            'waitingOrders'
        ));
    }
}