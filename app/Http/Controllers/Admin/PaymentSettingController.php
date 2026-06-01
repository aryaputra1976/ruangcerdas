<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PaymentSettingService;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
    public function __construct(
        protected PaymentSettingService $paymentSettingService
    ) {
    }

    public function edit()
    {
        $paymentSetting = $this->paymentSettingService->firstOrCreate();

        return view('admin.payment-settings.edit', compact('paymentSetting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'bank_account_holder' => ['nullable', 'string', 'max:255'],
            'bank_accounts' => ['nullable', 'array'],
            'bank_accounts.*.bank_name' => ['nullable', 'string', 'max:255'],
            'bank_accounts.*.account_number' => ['nullable', 'string', 'max:255'],
            'bank_accounts.*.account_holder' => ['nullable', 'string', 'max:255'],
            'bank_accounts.*.is_primary' => ['nullable', 'boolean'],
            'payment_note' => ['nullable', 'string'],
            'qris_image' => ['nullable', 'image', 'max:3072'],
            'remove_qris' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $paymentSetting = $this->paymentSettingService->firstOrCreate();

        if ($request->boolean('remove_qris') && $paymentSetting->qris_image_path) {
            Storage::disk('public')->delete($paymentSetting->qris_image_path);
            $paymentSetting->qris_image_path = null;
        }

        if ($request->hasFile('qris_image')) {
            if ($paymentSetting->qris_image_path) {
                Storage::disk('public')->delete($paymentSetting->qris_image_path);
            }

            $paymentSetting->qris_image_path = $request->file('qris_image')->store('payment/qris', 'public');
        }

        $bankAccounts = collect($validated['bank_accounts'] ?? [])
            ->map(function ($row) {
                return [
                    'bank_name' => trim((string) data_get($row, 'bank_name', '')),
                    'account_number' => trim((string) data_get($row, 'account_number', '')),
                    'account_holder' => trim((string) data_get($row, 'account_holder', '')),
                    'is_primary' => (bool) data_get($row, 'is_primary', false),
                ];
            })
            ->filter(fn ($row) => $row['bank_name'] !== '' && $row['account_number'] !== '' && $row['account_holder'] !== '')
            ->values();

        if ($bankAccounts->isNotEmpty()) {
            $primaryIndex = $bankAccounts->search(fn ($row) => $row['is_primary'] === true);
            if ($primaryIndex === false) {
                $primaryIndex = 0;
            }

            $bankAccounts = $bankAccounts->values()->map(function ($row, $index) use ($primaryIndex) {
                $row['is_primary'] = $index === $primaryIndex;
                return $row;
            })->values();
        }

        $paymentSetting->bank_name = $validated['bank_name'] ?? null;
        $paymentSetting->bank_account_number = $validated['bank_account_number'] ?? null;
        $paymentSetting->bank_account_holder = $validated['bank_account_holder'] ?? null;
        $paymentSetting->bank_accounts = $bankAccounts->all();
        $paymentSetting->payment_note = $validated['payment_note'] ?? null;
        $paymentSetting->is_active = $request->boolean('is_active');
        $paymentSetting->save();

        ActivityLogger::log(
            'payment_settings.updated',
            $paymentSetting,
            'Admin memperbarui pengaturan pembayaran.'
        );

        return redirect()
            ->route('admin.payment-settings.edit')
            ->with('success', 'Pengaturan pembayaran berhasil diperbarui.');
    }
}
