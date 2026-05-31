<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(): View
    {
        return view('public.order-tracking.index');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'invoice_number' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:150'],
        ]);

        $invoiceNumber = trim($validated['invoice_number']);
        $contact = trim($validated['contact']);

        $order = Order::query()
            ->with('product')
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (! $order || ! $this->contactMatches($order, $contact)) {
            return back()
                ->withInput()
                ->withErrors([
                    'tracking' => 'Order tidak ditemukan. Periksa kembali nomor invoice dan email/WhatsApp.',
                ]);
        }

        return view('public.order-tracking.show', compact('order'));
    }

    private function contactMatches(Order $order, string $contact): bool
    {
        $normalizedInput = mb_strtolower($contact);

        if (filled($order->buyer_email) && mb_strtolower((string) $order->buyer_email) === $normalizedInput) {
            return true;
        }

        return $this->normalizeWhatsapp((string) $order->buyer_whatsapp) === $this->normalizeWhatsapp($contact);
    }

    private function normalizeWhatsapp(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }
}
