<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductViewEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

