@include('emails.orders.layout', [
    'title' => 'Bukti Pembayaran Diterima - ' . $order->invoice_number,
    'heading' => 'Bukti Pembayaran Sudah Diterima',
    'order' => $order,
    'introLines' => [
        'Bukti pembayaran Anda sudah berhasil diunggah.',
        'Saat ini order sedang menunggu verifikasi admin Ruang Cerdas.',
    ],
    'details' => [
        'Invoice' => $order->invoice_number,
        'Produk' => $order->product?->name ?? '-',
        'Total Pembayaran' => \App\Support\Money::format($order->price ?? 0),
        'Status' => 'Menunggu verifikasi admin',
    ],
    'notice' => 'Tidak perlu mengirim ulang bukti pembayaran kecuali admin meminta Anda untuk melakukannya.',
    'buttons' => [
        [
            'label' => 'Status Order',
            'url' => $orderLookupUrl,
        ],
    ],
])
