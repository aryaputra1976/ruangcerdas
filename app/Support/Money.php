<?php

namespace App\Support;

class Money
{
    public static function format(int|float|null $amount): string
    {
        $amount = $amount ?? 0;

        return 'Rp' . number_format($amount, 0, ',', '.');
    }

    public static function rupiah(int|float|null $amount): string
    {
        return self::format($amount);
    }
}