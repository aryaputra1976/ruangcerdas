@php
    $downloadExpiry = $order->download_expires_at?->timezone(config('app.timezone'))->format('d M Y H:i');
@endphp

@include('emails.orders.layout', [
    'title' => 'Pembayaran Disetujui - ' . $order->invoice_number,
    'heading' => 'Pembayaran Sudah Disetujui',
    'order' => $order,
    'introLines' => [
        'Pembayaran Anda telah diverifikasi. Produk digital Anda sekarang sudah bisa diakses.',
    ],
    'details' => array_filter([
        'Invoice' => $order->invoice_number,
        'Produk' => $order->product?->name ?? '-',
        'Total Pembayaran' => \App\Support\Money::format($order->price ?? 0),
        'Link Aktif Sampai' => $downloadExpiry,
    ]),
    'notice' => 'Gunakan link download selama token masih aktif dan belum melewati batas akses.',
    'buttons' => [
        [
            'label' => 'Download Produk',
            'url' => $downloadUrl,
            'background' => '#059669',
        ],
        [
            'label' => 'Cek Order',
            'url' => $orderLookupUrl,
            'background' => '#0f172a',
        ],
    ],
])
