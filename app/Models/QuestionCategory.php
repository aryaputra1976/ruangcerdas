<?php

namespace App\Models;

use App\Support\TryoutBlueprint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'tryout_type',
        'section',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getSectionLabelAttribute(): string
    {
        return TryoutBlueprint::sectionLabel($this->tryout_type, $this->section);
    }

    public function setSectionAttribute($value): void
    {
        if (! filled($value)) {
            $this->attributes['section'] = null;

            return;
        }

        $normalized = strtolower((string) $value);
        $driver = static::resolveConnection($this->getConnectionName())->getDriverName();
        $this->attributes['section'] = $driver === 'sqlite' && in_array($normalized, ['twk', 'tiu', 'tkp'], true)
            ? strtoupper($normalized)
            : $normalized;
    }

    public function setTryoutTypeAttribute($value): void
    {
        $this->attributes['tryout_type'] = TryoutBlueprint::normalizeType($value);
    }
}
