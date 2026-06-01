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

        $bankAccounts = collect($setting?->bank_accounts ?? [])
            ->map(function ($row, $index) {
                return [
                    'bank_name' => trim((string) data_get($row, 'bank_name', '')),
                    'account_number' => trim((string) data_get($row, 'account_number', '')),
                    'account_holder' => trim((string) data_get($row, 'account_holder', '')),
                    'is_primary' => (bool) data_get($row, 'is_primary', $index === 0),
                ];
            })
            ->filter(fn ($row) => $row['bank_name'] !== '' && $row['account_number'] !== '' && $row['account_holder'] !== '')
            ->values();

        if ($bankAccounts->isEmpty()) {
            $legacyBankName = trim((string) ($setting?->bank_name ?? config('ruangcerdas.payment.bank_name')));
            $legacyAccountNumber = trim((string) ($setting?->bank_account_number ?? config('ruangcerdas.payment.bank_account_number')));
            $legacyAccountHolder = trim((string) ($setting?->bank_account_holder ?? config('ruangcerdas.payment.bank_account_holder')));

            if ($legacyBankName !== '' && $legacyAccountNumber !== '' && $legacyAccountHolder !== '') {
                $bankAccounts = collect([[
                    'bank_name' => $legacyBankName,
                    'account_number' => $legacyAccountNumber,
                    'account_holder' => $legacyAccountHolder,
                    'is_primary' => true,
                ]]);
            }
        }

        return [
            'bank_name' => $setting?->bank_name ?? config('ruangcerdas.payment.bank_name'),
            'bank_account_number' => $setting?->bank_account_number ?? config('ruangcerdas.payment.bank_account_number'),
            'bank_account_holder' => $setting?->bank_account_holder ?? config('ruangcerdas.payment.bank_account_holder'),
            'bank_accounts' => $bankAccounts->all(),
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
