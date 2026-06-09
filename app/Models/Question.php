<?php

namespace App\Models;

use App\Support\TryoutBlueprint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question_category_id',
        'tryout_type',
        'section',
        'question_text',
        'explanation',
        'difficulty',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'question_category_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('option_label');
    }

    public function tryoutAnswers(): HasMany
    {
        return $this->hasMany(TryoutAnswer::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getSectionLabelAttribute(): string
    {
        return TryoutBlueprint::sectionLabel($this->tryout_type, $this->section);
    }

    public function usesWeightedScoring(): bool
    {
        return TryoutBlueprint::scoringMode($this->tryout_type, strtolower((string) $this->section)) === 'weighted';
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
