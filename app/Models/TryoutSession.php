<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    public function sectionResults(): Collection
    {
        $this->loadMissing(['package', 'answers.question']);

        return collect($this->package?->sectionSummaries() ?? [])
            ->map(function (array $section) {
                $sectionKey = strtolower((string) ($section['key'] ?? ''));
                $answers = $this->answers->filter(
                    fn (TryoutAnswer $answer) => strtolower((string) $answer->question?->section) === $sectionKey
                );
                $correctCount = ($section['scoring_mode'] ?? 'single_correct') === 'weighted'
                    ? null
                    : $answers->filter(fn (TryoutAnswer $answer) => (int) $answer->score === 5)->count();

                return [
                    'key' => $sectionKey,
                    'label' => $section['label'] ?? strtoupper($sectionKey),
                    'score' => $this->scoreForSection($sectionKey),
                    'threshold' => $section['threshold'] ?? null,
                    'question_count' => $answers->count(),
                    'correct_count' => $correctCount,
                    'incorrect_count' => $correctCount === null ? null : max($answers->count() - $correctCount, 0),
                    'scoring_mode' => $section['scoring_mode'] ?? 'single_correct',
                ];
            })
            ->values();
    }

    public function hasPassingThreshold(): bool
    {
        $thresholds = $this->package?->sectionThresholds() ?? [];
        $totalThreshold = $this->package?->totalThreshold();

        return collect($thresholds)->filter(fn ($value) => $value !== null)->isNotEmpty()
            || $totalThreshold !== null;
    }

    public function isPassed(): ?bool
    {
        if (! $this->isFinished()) {
            return null;
        }

        $this->loadMissing('package');

        if (! $this->hasPassingThreshold()) {
            return null;
        }

        $sectionResults = $this->sectionResults();
        $sectionsPassed = $sectionResults
            ->filter(fn (array $section) => $section['threshold'] !== null)
            ->every(fn (array $section) => (int) $section['score'] >= (int) $section['threshold']);

        $totalThreshold = $this->package?->totalThreshold();
        $totalPassed = $totalThreshold === null ? true : (int) $this->total_score >= (int) $totalThreshold;

        return $sectionsPassed && $totalPassed;
    }
}
