<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

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

        $orders = Order::query()
            ->with('product')
            ->when($filters['from'], fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['product_id'], fn ($query, $productId) => $query->where('product_id', $productId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

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

        return view('admin.reports.index', compact('orders', 'products', 'filters', 'statusOptions'));
    }
}
