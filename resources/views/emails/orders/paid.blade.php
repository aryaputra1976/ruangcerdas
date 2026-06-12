@php
    $downloadExpiry = $order->download_expires_at?->timezone(config('app.timezone'))->format('d M Y H:i');
    $isTryoutOrder = $isTryoutOrder ?? false;
@endphp

@include('emails.orders.layout', [
    'title' => 'Pembayaran Disetujui - ' . $order->invoice_number,
    'heading' => 'Pembayaran Sudah Disetujui',
    'order' => $order,
    'eyebrow' => $isTryoutOrder ? 'AKSES TRYOUT' : 'RUANG AKSES',
    'introLines' => $isTryoutOrder
        ? [
            'Pembayaran Anda telah diverifikasi. Akses tryout premium sekarang sudah aktif untuk email pembelian ini.',
        ]
        : [
            'Pembayaran Anda telah diverifikasi. Produk digital Anda sekarang sudah bisa diakses melalui Ruang Akses.',
        ],
    'details' => array_filter([
        'Invoice' => $order->invoice_number,
        'Email Akses' => $order->buyer_email,
        'Produk' => $order->product?->name ?? '-',
        'Total Pembayaran' => \App\Support\Money::format($order->price ?? 0),
        'Akses Berlaku Sampai' => $isTryoutOrder ? null : $downloadExpiry,
    ]),
    'notice' => $isTryoutOrder
        ? 'Buka halaman tryout, pilih paket yang sesuai, lalu mulai dengan email pembeli yang sama.'
        : 'Masukkan invoice dan email pembeli yang sama ke Ruang Akses untuk membuka file digital Anda.',
    'buttons' => $isTryoutOrder
        ? array_values(array_filter([
            $tryoutListingUrl ? [
                'label' => 'Mulai Tryout Sekarang',
                'url' => $tryoutListingUrl,
                'background' => '#059669',
            ] : null,
            $tryoutPackageUrl ? [
                'label' => 'Lihat Halaman Paket',
                'url' => $tryoutPackageUrl,
                'background' => '#0f172a',
            ] : null,
            [
                'label' => 'Status Order',
                'url' => $orderLookupUrl,
                'background' => '#1d4ed8',
            ],
        ]))
        : [
            [
                'label' => 'Buka Ruang Akses',
                'url' => $downloadRoomUrl,
                'background' => '#059669',
            ],
            [
                'label' => 'Status Order',
                'url' => $orderLookupUrl,
                'background' => '#0f172a',
            ],
        ],
    'outroLines' => $isTryoutOrder
        ? [
            'Simpan invoice dan gunakan email pembeli yang sama saat mulai tryout premium.',
        ]
        : [
            'Simpan invoice dan email pembeli ini karena dipakai untuk membuka Ruang Akses.',
        ],
])
