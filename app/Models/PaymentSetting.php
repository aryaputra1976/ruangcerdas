<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'qris_image_path',
        'payment_note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
