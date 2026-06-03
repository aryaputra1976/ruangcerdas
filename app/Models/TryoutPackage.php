<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TryoutPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'is_free',
        'duration_minutes',
        'twk_count',
        'tiu_count',
        'tkp_count',
        'access_days',
        'attempt_limit',
        'has_explanation',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_free' => 'boolean',
        'duration_minutes' => 'integer',
        'twk_count' => 'integer',
        'tiu_count' => 'integer',
        'tkp_count' => 'integer',
        'access_days' => 'integer',
        'attempt_limit' => 'integer',
        'has_explanation' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(TryoutSession::class);
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(TryoutAccess::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function checkoutProduct()
    {
        if ($this->is_free) {
            return null;
        }

        return Product::query()->where('slug', $this->slug)->first();
    }

    public function getTotalQuestionsAttribute(): int
    {
        return (int) $this->twk_count + (int) $this->tiu_count + (int) $this->tkp_count;
    }
}
