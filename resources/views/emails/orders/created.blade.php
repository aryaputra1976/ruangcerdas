@php
    $bankAccounts = collect($paymentConfig['bank_accounts'] ?? []);
    $primaryBank = $bankAccounts->firstWhere('is_primary', true) ?? $bankAccounts->first();
    $paymentNote = trim((string) ($paymentConfig['payment_note'] ?? 'Transfer sesuai nominal invoice agar verifikasi lebih cepat.'));
    $bankInstruction = $primaryBank
        ? $primaryBank['bank_name'] . ' a.n. ' . $primaryBank['account_holder'] . ' (' . $primaryBank['account_number'] . ')'
        : 'Ikuti instruksi pembayaran pada halaman upload bukti pembayaran.';
@endphp

@include('emails.orders.layout', [
    'title' => 'Order Baru - ' . $order->invoice_number,
    'heading' => 'Order Berhasil Dibuat',
    'order' => $order,
    'introLines' => [
        'Terima kasih, order Anda sudah tercatat di Ruang Cerdas.',
        'Silakan selesaikan pembayaran lalu upload bukti pembayaran agar admin bisa memverifikasi order Anda.',
    ],
    'details' => [
        'Invoice' => $order->invoice_number,
        'Produk' => $order->product?->name ?? '-',
        'Total Pembayaran' => \App\Support\Money::format($order->price ?? 0),
        'Instruksi Pembayaran' => $bankInstruction,
    ],
    'notice' => $paymentNote,
    'buttons' => [
        [
            'label' => 'Upload Bukti Pembayaran',
            'url' => $uploadPaymentUrl,
        ],
        [
            'label' => 'Status Order',
            'url' => $orderLookupUrl,
            'background' => '#0f172a',
        ],
    ],
    'outroLines' => [
        'Simpan nomor invoice ini untuk upload bukti bayar dan cek status order kapan saja.',
    ],
])
