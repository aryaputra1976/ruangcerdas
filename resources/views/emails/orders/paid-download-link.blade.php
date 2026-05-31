<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Disetujui</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;">
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">
                                Halo {{ $order->buyer_name }},
                            </p>

                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;">
                                Pembayaran Anda telah disetujui. Berikut detail pesanan Anda:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 18px 0;border-collapse:collapse;">
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#475569;width:180px;">Invoice</td>
                                    <td style="padding:8px 0;font-size:14px;font-weight:600;">{{ $order->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#475569;">Produk</td>
                                    <td style="padding:8px 0;font-size:14px;font-weight:600;">{{ $order->product?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#475569;">Total Pembayaran</td>
                                    <td style="padding:8px 0;font-size:14px;font-weight:600;">Rp {{ number_format((int) $order->price, 0, ',', '.') }}</td>
                                </tr>
                                @if ($order->download_expires_at)
                                    <tr>
                                        <td style="padding:8px 0;font-size:14px;color:#475569;">Link Aktif Sampai</td>
                                        <td style="padding:8px 0;font-size:14px;font-weight:600;">
                                            {{ $order->download_expires_at->timezone(config('app.timezone'))->format('d M Y H:i') }}
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <p style="margin:0 0 16px 0;">
                                <a href="{{ $downloadUrl }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;padding:11px 18px;border-radius:8px;font-size:14px;font-weight:700;">
                                    Download Produk
                                </a>
                            </p>

                            <p style="margin:0 0 16px 0;font-size:13px;line-height:1.7;color:#334155;">
                                Link download hanya dapat digunakan selama token masih valid, belum expired, dan belum melewati batas jumlah download yang ditetapkan.
                            </p>

                            <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6;">
                                Terima kasih telah berbelanja di Ruang Cerdas.
                            </p>

                            <p style="margin:0;font-size:14px;line-height:1.6;">
                                Salam profesional,<br>
                                <strong>Ruang Cerdas</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
