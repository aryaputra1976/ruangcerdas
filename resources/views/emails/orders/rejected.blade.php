@include('emails.orders.layout', [
    'title' => 'Pembayaran Ditolak - ' . $order->invoice_number,
    'heading' => 'Pembayaran Belum Bisa Disetujui',
    'order' => $order,
    'introLines' => [
        'Pembayaran untuk order Anda belum dapat kami setujui saat ini.',
        'Silakan periksa alasan penolakan berikut lalu upload ulang bukti pembayaran yang lebih sesuai.',
    ],
    'details' => [
        'Invoice' => $order->invoice_number,
        'Produk' => $order->product?->name ?? '-',
        'Total Pembayaran' => \App\Support\Money::format($order->price ?? 0),
        'Alasan Penolakan' => $order->rejection_reason ?: '-',
    ],
    'buttons' => [
        [
            'label' => 'Upload Ulang Bukti',
            'url' => $uploadPaymentUrl,
        ],
        [
            'label' => 'Cek Order',
            'url' => $orderLookupUrl,
            'background' => '#0f172a',
        ],
    ],
])
