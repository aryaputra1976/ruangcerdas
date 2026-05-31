<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:150'],
            'buyer_email' => ['required', 'email', 'max:150'],
            'buyer_whatsapp' => ['required', 'string', 'max:30'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'buyer_name.required' => 'Nama lengkap wajib diisi.',
            'buyer_email.required' => 'Email wajib diisi.',
            'buyer_email.email' => 'Format email tidak valid.',
            'buyer_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        ];
    }
}
