<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerContactController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'status' => (string) $request->query('status', 'all'),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ];

        $orders = Order::query()
            ->with('product:id,name')
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($sub) use ($filters) {
                    $sub->where('buyer_name', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('buyer_email', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('buyer_whatsapp', 'like', '%' . $filters['q'] . '%');
                });
            })
            ->when($filters['from'], fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at')
            ->get();

        $customers = $orders
            ->groupBy(fn (Order $order) => $this->customerGroupKey($order))
            ->map(function (Collection $group) {
                $latestOrder = $group->sortByDesc('created_at')->first();
                $paidOrders = $group->where('status', Order::STATUS_PAID);
                $pendingOrders = $group->where('status', Order::STATUS_PENDING);
                $rejectedOrders = $group->where('status', Order::STATUS_REJECTED);

                $email = $group->pluck('buyer_email')->filter()->first();
                $whatsapp = $group->pluck('buyer_whatsapp')->filter()->first();
                $whatsappForWa = $this->normalizeWhatsappForWa($whatsapp);

                return [
                    'name' => $group->pluck('buyer_name')->filter()->first() ?: '-',
                    'email' => $email,
                    'whatsapp' => $whatsapp,
                    'whatsapp_wa' => $whatsappForWa,
                    'total_orders' => $group->count(),
                    'total_paid_orders' => $paidOrders->count(),
                    'total_pending_orders' => $pendingOrders->count(),
                    'total_rejected_orders' => $rejectedOrders->count(),
                    'total_paid_revenue' => (int) $paidOrders->sum('price'),
                    'last_order_at' => $latestOrder?->created_at,
                    'last_product_name' => $latestOrder?->product?->name ?: '-',
                    'order_query' => $email ?: ($whatsapp ?: ''),
                    'first_order_at' => $group->min('created_at'),
                ];
            })
            ->values();

        $customers = match ($filters['status']) {
            'has_paid' => $customers->where('total_paid_orders', '>', 0)->values(),
            'pending_only' => $customers
                ->where('total_paid_orders', 0)
                ->where('total_pending_orders', '>', 0)
                ->values(),
            default => $customers,
        };

        $customers = $customers
            ->sortByDesc('last_order_at')
            ->values();

        $summary = [
            'total_customers' => $customers->count(),
            'customers_has_paid' => $customers->where('total_paid_orders', '>', 0)->count(),
            'total_paid_revenue' => (int) $customers->sum('total_paid_revenue'),
            'total_orders' => (int) $customers->sum('total_orders'),
            'new_customers_30_days' => $customers
                ->filter(fn ($customer) => $customer['first_order_at'] && $customer['first_order_at']->gte(now()->subDays(30)))
                ->count(),
        ];

        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $customers->forPage($currentPage, $perPage)->values();

        $paginatedCustomers = new LengthAwarePaginator(
            $pagedItems,
            $customers->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('admin.customers.index', [
            'customers' => $paginatedCustomers,
            'filters' => $filters,
            'summary' => $summary,
        ]);
    }

    private function customerGroupKey(Order $order): string
    {
        $email = strtolower(trim((string) $order->buyer_email));
        if ($email !== '') {
            return 'email:' . $email;
        }

        $whatsapp = preg_replace('/\D+/', '', (string) $order->buyer_whatsapp);
        if ($whatsapp !== '') {
            return 'wa:' . $whatsapp;
        }

        return 'order:' . $order->id;
    }

    private function normalizeWhatsappForWa(?string $whatsapp): ?string
    {
        if (blank($whatsapp)) {
            return null;
        }

        $normalized = preg_replace('/\D+/', '', (string) $whatsapp);
        if ($normalized === '') {
            return null;
        }

        if (str_starts_with($normalized, '08')) {
            return '62' . substr($normalized, 1);
        }

        if (str_starts_with($normalized, '620')) {
            return '62' . substr($normalized, 3);
        }

        return $normalized;
    }
}
