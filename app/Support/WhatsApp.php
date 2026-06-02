<?php

namespace App\Support;

class WhatsApp
{
    public static function normalizeIndonesiaNumber(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        if (! str_starts_with($digits, '62')) {
            return null;
        }

        return $digits;
    }

    public static function waMeUrl(?string $raw, ?string $message = null): ?string
    {
        $normalized = self::normalizeIndonesiaNumber($raw);

        if ($normalized === null) {
            return null;
        }

        $url = 'https://wa.me/' . $normalized;
        $trimmedMessage = trim((string) $message);

        if ($trimmedMessage !== '') {
            $url .= '?text=' . rawurlencode($trimmedMessage);
        }

        return $url;
    }
}

