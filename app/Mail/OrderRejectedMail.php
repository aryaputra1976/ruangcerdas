<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRejectedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('product');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . config('app.name', 'Ruang Cerdas') . '] Pembayaran Ditolak - ' . $this->order->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.rejected',
            with: [
                'order' => $this->order,
                'uploadPaymentUrl' => route('orders.payment.form', $this->order->invoice_number),
                'orderLookupUrl' => route('public.orders.lookup'),
            ],
        );
    }
}
