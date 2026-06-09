<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaidMail extends Mailable
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
            subject: '[' . config('app.name', 'Ruang Cerdas') . '] Pembayaran Disetujui - ' . $this->order->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.paid',
            with: [
                'order' => $this->order,
                'downloadUrl' => route('orders.download', [
                    'invoice' => $this->order->invoice_number,
                    'token' => $this->order->download_token,
                ]),
                'orderLookupUrl' => route('public.orders.lookup'),
            ],
        );
    }
}
