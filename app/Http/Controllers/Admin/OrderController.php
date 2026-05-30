<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::query()
            ->with('product')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
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
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'status'));
    }

    public function show(Order $order)
    {
        $order->load('product.category', 'approver');

        return view('admin.orders.show', compact('order'));
    }

    public function approve(Request $request, Order $order)
    {
        abort_if($order->isPaid(), 422, 'Order sudah paid.');

        $order->update([
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'download_token' => $order->download_token ?: Str::random(64),
            'download_expires_at' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Pembayaran berhasil di-approve. Link download sudah aktif.');
    }

    public function reject(Request $request, Order $order)
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_if($order->isPaid(), 422, 'Order yang sudah paid tidak bisa ditolak.');

        $order->update([
            'status' => Order::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order berhasil ditolak.');
    }
}