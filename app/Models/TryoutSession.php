<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TryoutSession extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'user_id',
        'tryout_package_id',
        'participant_name',
        'participant_email',
        'started_at',
        'finished_at',
        'duration_minutes',
        'status',
        'twk_score',
        'tiu_score',
        'tkp_score',
        'section_scores',
        'total_score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration_minutes' => 'integer',
        'twk_score' => 'integer',
        'tiu_score' => 'integer',
        'tkp_score' => 'integer',
        'section_scores' => 'array',
        'total_score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TryoutPackage::class, 'tryout_package_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TryoutAnswer::class)->orderBy('id');
    }

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function endsAt()
    {
        $startedAt = $this->started_at ?? $this->created_at ?? now();

        return $startedAt->copy()->addMinutes((int) $this->duration_minutes);
    }

    public function isExpired(): bool
    {
        $endsAt = $this->endsAt();

        return $endsAt !== null && now()->greaterThanOrEqualTo($endsAt);
    }

    public function scoreForSection(string $section): int
    {
        return (int) data_get($this->section_scores ?? [], $section, 0);
    }
}
