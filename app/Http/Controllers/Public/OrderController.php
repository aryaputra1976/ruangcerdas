<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Order;
use App\Services\PaymentSettingService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function thankYou(PaymentSettingService $paymentSettingService, string $invoice)
    {
        $order = Order::query()
            ->with('product.category')
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        return view('public.orders.thank-you', [
            'order' => $order,
            'paymentConfig' => $paymentSettingService->current(),
        ]);
    }

    public function paymentForm(PaymentSettingService $paymentSettingService, string $invoice)
    {
        $order = Order::query()
            ->with('product.category')
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        abort_if($order->isPaid(), 403, 'Order ini sudah dibayar.');

        return view('public.orders.upload-payment', [
            'order' => $order,
            'paymentConfig' => $paymentSettingService->current(),
        ]);
    }

    public function uploadPayment(UploadPaymentProofRequest $request, string $invoice)
    {
        $order = Order::query()
            ->with('product')
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        abort_if($order->isPaid(), 403, 'Order ini sudah dibayar.');

        $file = $request->file('payment_proof');

        $extension = $file->getClientOriginalExtension();

        $filename = Str::slug($order->invoice_number)
            . '-'
            . now()->format('YmdHis')
            . '.'
            . $extension;

        if ($order->payment_proof_path) {
            Storage::disk('public')->delete($order->payment_proof_path);
        }

        $path = $file->storeAs(
            'payment-proofs/' . now()->format('Y/m'),
            $filename,
            'public'
        );

        $order->update([
            'payment_proof_path' => $path,
            'payment_uploaded_at' => now(),
            'payment_note' => $request->input('payment_note'),
            'status' => Order::STATUS_PAYMENT_UPLOADED,
        ]);

        return redirect()
            ->route('orders.thank-you', $order->invoice_number)
            ->with('success', 'Bukti pembayaran berhasil diupload. Admin akan melakukan verifikasi.');
    }
}
