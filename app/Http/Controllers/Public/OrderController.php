<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\LandingSetting;
use App\Models\Order;
use App\Services\PaymentSettingService;
use App\Services\TryoutAccessService;
use App\Support\OrderAuditLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function thankYou(
        PaymentSettingService $paymentSettingService,
        TryoutAccessService $tryoutAccessService,
        string $invoice
    )
    {
        $order = Order::query()
            ->with('product.category')
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        $tryoutPackage = $tryoutAccessService->resolvePackageFromOrder($order);
        $tryoutAccess = null;

        if ($order->isPaid() && $tryoutPackage) {
            $tryoutAccess = $tryoutAccessService->ensureAccessFromPaidOrder($order);
        }

        return view('public.orders.thank-you', [
            'order' => $order,
            'paymentConfig' => $paymentSettingService->current(),
            'supportWhatsapp' => LandingSetting::query()->value('support_whatsapp'),
            'tryoutPackage' => $tryoutPackage,
            'tryoutAccess' => $tryoutAccess,
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
            'supportWhatsapp' => LandingSetting::query()->value('support_whatsapp'),
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

        $extension = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            default => $file->getClientOriginalExtension(),
        };

        $filename = Str::slug($order->invoice_number)
            . '-'
            . now()->format('YmdHis')
            . '.'
            . $extension;

        if ($order->payment_proof_path) {
            Storage::disk('private')->delete($order->payment_proof_path);
            Storage::disk('public')->delete($order->payment_proof_path);
        }

        $path = $file->storeAs(
            'payment-proofs/' . now()->format('Y/m'),
            $filename,
            'private'
        );

        $fromStatus = $order->status;

        $order->update([
            'payment_proof_path' => $path,
            'payment_uploaded_at' => now(),
            'payment_note' => $request->input('payment_note'),
            'status' => Order::STATUS_PAYMENT_UPLOADED,
        ]);

        OrderAuditLogger::log(
            $order,
            'payment_proof.uploaded',
            'Pembeli mengunggah bukti pembayaran.',
            [
                'uploaded_at' => optional($order->payment_uploaded_at)->toDateTimeString(),
                'original_filename' => $file->getClientOriginalName(),
                'note' => $order->payment_note,
            ],
            $fromStatus,
            $order->status
        );

        return redirect()
            ->route('orders.thank-you', $order->invoice_number)
            ->with('success', 'Bukti pembayaran berhasil diupload. Admin akan melakukan verifikasi.');
    }
}
