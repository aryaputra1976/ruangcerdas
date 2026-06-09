<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\PaymentSettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $paymentConfig;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('product');
        $this->paymentConfig = app(PaymentSettingService::class)->current();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . config('app.name', 'Ruang Cerdas') . '] Order Baru - ' . $this->order->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.created',
            with: [
                'order' => $this->order,
                'paymentConfig' => $this->paymentConfig,
                'uploadPaymentUrl' => route('orders.payment.form', $this->order->invoice_number),
                'orderLookupUrl' => route('public.orders.lookup'),
            ],
        );
    }
}
