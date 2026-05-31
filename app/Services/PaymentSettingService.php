<?php

namespace App\Services;

use App\Models\PaymentSetting;

class PaymentSettingService
{
    public function current(): array
    {
        $setting = PaymentSetting::query()
            ->where('is_active', true)
            ->first();

        return [
            'bank_name' => $setting?->bank_name ?? config('ruangcerdas.payment.bank_name'),
            'bank_account_number' => $setting?->bank_account_number ?? config('ruangcerdas.payment.bank_account_number'),
            'bank_account_holder' => $setting?->bank_account_holder ?? config('ruangcerdas.payment.bank_account_holder'),
            'qris_image_path' => $setting?->qris_image_path
                ?? config('ruangcerdas.payment.qris_image_path')
                ?? config('ruangcerdas.payment.qris_image'),
            'payment_note' => $setting?->payment_note ?? config('ruangcerdas.payment.payment_note'),
            'is_active' => $setting?->is_active ?? true,
        ];
    }

    public function firstOrCreate(): PaymentSetting
    {
        return PaymentSetting::query()->firstOrCreate(
            ['is_active' => true],
            [
                'bank_name' => config('ruangcerdas.payment.bank_name'),
                'bank_account_number' => config('ruangcerdas.payment.bank_account_number'),
                'bank_account_holder' => config('ruangcerdas.payment.bank_account_holder'),
                'qris_image_path' => config('ruangcerdas.payment.qris_image_path')
                    ?? config('ruangcerdas.payment.qris_image'),
                'payment_note' => config('ruangcerdas.payment.payment_note'),
            ]
        );
    }
}
