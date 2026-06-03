<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TryoutAccess extends Model
{
    protected $fillable = [
        'tryout_package_id',
        'order_id',
        'buyer_email',
        'starts_at',
        'expires_at',
        'remaining_attempts',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'remaining_attempts' => 'integer',
        'is_active' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TryoutPackage::class, 'tryout_package_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('remaining_attempts', '>', 0)
            ->where(function (Builder $builder) {
                $builder->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function isCurrentlyActive(): bool
    {
        return (bool) $this->is_active
            && (int) $this->remaining_attempts > 0
            && ($this->expires_at === null || $this->expires_at->gte(now()));
    }
}
