<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PaymentSettingService;
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

        $paymentSetting->bank_name = $validated['bank_name'] ?? null;
        $paymentSetting->bank_account_number = $validated['bank_account_number'] ?? null;
        $paymentSetting->bank_account_holder = $validated['bank_account_holder'] ?? null;
        $paymentSetting->payment_note = $validated['payment_note'] ?? null;
        $paymentSetting->is_active = $request->boolean('is_active');
        $paymentSetting->save();

        return redirect()
            ->route('admin.payment-settings.edit')
            ->with('success', 'Pengaturan pembayaran berhasil diperbarui.');
    }
}
