<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderLookupController extends Controller
{
    public function index(): View
    {
        return view('public.orders.lookup');
    }

    public function submit(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'invoice_number' => ['required', 'string', 'max:100'],
            'buyer_email' => ['required', 'email', 'max:150'],
        ]);

        $invoiceNumber = trim($validated['invoice_number']);
        $buyerEmail = mb_strtolower(trim($validated['buyer_email']));

        $order = Order::query()
            ->with('product')
            ->where('invoice_number', $invoiceNumber)
            ->whereRaw('LOWER(buyer_email) = ?', [$buyerEmail])
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->withErrors([
                    'lookup' => 'Order tidak ditemukan. Pastikan nomor invoice dan email sesuai.',
                ]);
        }

        return view('public.orders.lookup-result', compact('order'));
    }
}
