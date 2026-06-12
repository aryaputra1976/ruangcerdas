<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'Ruang Cerdas') }}</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;">
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 6px 0;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#2563eb;">
                                {{ config('app.name', 'Ruang Cerdas') }}
                            </p>

                            @if (!empty($eyebrow))
                                <div style="display:inline-block;margin:0 0 10px 0;padding:6px 10px;border-radius:999px;background:#eff6ff;border:1px solid #bfdbfe;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#1d4ed8;">
                                    {{ $eyebrow }}
                                </div>
                            @endif

                            <h1 style="margin:0 0 16px 0;font-size:24px;line-height:1.3;color:#0f172a;">
                                {{ $heading }}
                            </h1>

                            <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">
                                Halo {{ $order->buyer_name }},
                            </p>

                            @foreach (($introLines ?? []) as $line)
                                <p style="margin:0 0 12px 0;font-size:15px;line-height:1.6;">
                                    {{ $line }}
                                </p>
                            @endforeach

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0;border-collapse:collapse;">
                                @foreach (($details ?? []) as $label => $value)
                                    <tr>
                                        <td style="padding:8px 0;font-size:14px;color:#475569;width:200px;vertical-align:top;">{{ $label }}</td>
                                        <td style="padding:8px 0;font-size:14px;font-weight:600;vertical-align:top;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            @if (!empty($notice))
                                <div style="margin:0 0 16px 0;padding:12px 14px;border-radius:10px;background:#eff6ff;border:1px solid #bfdbfe;font-size:13px;line-height:1.7;color:#1e3a8a;">
                                    {{ $notice }}
                                </div>
                            @endif

                            @if (!empty($buttons))
                                <div style="margin:0 0 16px 0;">
                                    @foreach ($buttons as $button)
                                        <a href="{{ $button['url'] }}"
                                           style="display:inline-block;margin:0 10px 10px 0;background:{{ $button['background'] ?? '#2563eb' }};color:{{ $button['color'] ?? '#ffffff' }};text-decoration:none;padding:11px 18px;border-radius:8px;font-size:14px;font-weight:700;">
                                            {{ $button['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            @foreach (($outroLines ?? []) as $line)
                                <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6;color:#334155;">
                                    {{ $line }}
                                </p>
                            @endforeach

                            <p style="margin:0;font-size:14px;line-height:1.6;">
                                Salam profesional,<br>
                                <strong>{{ config('app.name', 'Ruang Cerdas') }}</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
