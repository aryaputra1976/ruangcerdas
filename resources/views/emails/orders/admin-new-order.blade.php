@include('emails.orders.layout', [
    'title' => 'Order Baru Masuk - ' . $order->invoice_number,
    'heading' => 'Order Baru Masuk',
    'order' => $order,
    'introLines' => [
        'Ada order baru yang baru saja dibuat oleh pembeli di Ruang Cerdas.',
    ],
    'details' => [
        'Invoice' => $order->invoice_number,
        'Produk' => $order->product?->name ?? '-',
        'Nama Pembeli' => $order->buyer_name,
        'Email Pembeli' => $order->buyer_email,
        'WhatsApp Pembeli' => $order->buyer_whatsapp,
        'Total' => \App\Support\Money::format($order->price ?? 0),
        'Status' => strtoupper((string) $order->status),
    ],
    'buttons' => [
        [
            'label' => 'Lihat Detail Order',
            'url' => $adminOrderUrl,
        ],
    ],
])
