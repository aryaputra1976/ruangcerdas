@include('emails.orders.layout', [
    'title' => 'Bukti Pembayaran Baru - ' . $order->invoice_number,
    'heading' => 'Bukti Pembayaran Baru Diupload',
    'order' => $order,
    'introLines' => [
        'Pembeli baru saja mengupload bukti pembayaran dan order menunggu verifikasi admin.',
    ],
    'details' => [
        'Invoice' => $order->invoice_number,
        'Produk' => $order->product?->name ?? '-',
        'Nama Pembeli' => $order->buyer_name,
        'Email Pembeli' => $order->buyer_email,
        'Total' => \App\Support\Money::format($order->price ?? 0),
        'Waktu Upload' => $order->payment_uploaded_at?->timezone(config('app.timezone'))->format('d M Y H:i') ?? '-',
        'Status' => strtoupper((string) $order->status),
    ],
    'buttons' => [
        [
            'label' => 'Verifikasi Order',
            'url' => $adminOrderUrl,
        ],
    ],
])
