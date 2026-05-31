<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaidDownloadLinkMail extends Mailable
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
            subject: 'Pembayaran Disetujui - ' . $this->order->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.paid-download-link',
            with: [
                'order' => $this->order,
                'downloadUrl' => route('orders.download', [
                    $this->order->invoice_number,
                    $this->order->download_token,
                ]),
            ],
        );
    }
}
