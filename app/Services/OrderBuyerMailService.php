<?php

namespace App\Services;

use App\Mail\OrderCreatedMail;
use App\Mail\OrderPaidMail;
use App\Mail\OrderRejectedMail;
use App\Mail\PaymentProofUploadedMail;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderBuyerMailService
{
    public function sendOrderCreated(Order $order): void
    {
        $this->send($order, new OrderCreatedMail($order), 'order_created');
    }

    public function sendPaymentProofUploaded(Order $order): void
    {
        $this->send($order, new PaymentProofUploadedMail($order), 'payment_proof_uploaded');
    }

    public function sendOrderPaid(Order $order): void
    {
        $this->send($order, new OrderPaidMail($order), 'order_paid');
    }

    public function sendOrderRejected(Order $order): void
    {
        $this->send($order, new OrderRejectedMail($order), 'order_rejected');
    }

    private function send(Order $order, Mailable $mailable, string $type): void
    {
        if (blank($order->buyer_email)) {
            return;
        }

        try {
            Mail::to($order->buyer_email)->send($mailable);
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim email status order ke pembeli.', [
                'type' => $type,
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'buyer_email' => $order->buyer_email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
