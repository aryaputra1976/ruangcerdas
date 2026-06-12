<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\TryoutPackage;
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
        $tryoutPackage = $this->resolveTryoutPackage();

        return new Content(
            view: 'emails.orders.paid',
            with: [
                'order' => $this->order,
                'tryoutPackage' => $tryoutPackage,
                'isTryoutOrder' => (bool) $tryoutPackage,
                'downloadRoomUrl' => route('public.download-room.index'),
                'orderLookupUrl' => route('public.orders.lookup'),
                'tryoutListingUrl' => $tryoutPackage ? route($tryoutPackage->listingRouteName()) : null,
                'tryoutPackageUrl' => $tryoutPackage
                    ? route('public.tryouts.packages.start', [
                        'tryoutType' => $tryoutPackage->routeSegment(),
                        'tryoutPackage' => $tryoutPackage,
                    ])
                    : null,
            ],
        );
    }

    private function resolveTryoutPackage(): ?TryoutPackage
    {
        $product = $this->order->product;

        if (! $product?->slug) {
            return null;
        }

        return TryoutPackage::query()
            ->where('slug', $product->slug)
            ->first();
    }
}
