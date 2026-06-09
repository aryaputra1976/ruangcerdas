<?php

namespace App\Services;

use App\Mail\AdminNewOrderMail;
use App\Mail\AdminPaymentProofUploadedMail;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderAdminMailService
{
    public function sendNewOrder(Order $order): void
    {
        $this->send($order, new AdminNewOrderMail($order), 'admin_new_order');
    }

    public function sendPaymentProofUploaded(Order $order): void
    {
        $this->send($order, new AdminPaymentProofUploadedMail($order), 'admin_payment_proof_uploaded');
    }

    private function send(Order $order, Mailable $mailable, string $type): void
    {
        $adminAddress = trim((string) config('mail.admin_address'));

        if ($adminAddress === '') {
            return;
        }

        try {
            Mail::to($adminAddress)->send($mailable);
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim email notifikasi admin order.', [
                'type' => $type,
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'admin_address' => $adminAddress,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
