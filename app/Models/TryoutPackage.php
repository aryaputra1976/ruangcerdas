<?php

namespace App\Models;

use App\Support\TryoutBlueprint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TryoutPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'tryout_type',
        'position_target',
        'description',
        'price',
        'is_free',
        'duration_minutes',
        'twk_count',
        'tiu_count',
        'tkp_count',
        'section_composition',
        'section_thresholds',
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
        'section_composition' => 'array',
        'section_thresholds' => 'array',
        'access_days' => 'integer',
        'attempt_limit' => 'integer',
        'has_explanation' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getIsFreeAttribute($value): bool
    {
        return $this->isFreePackage($value);
    }

    public function isFreePackage(mixed $value = null): bool
    {
        return (bool) ($value ?? $this->attributes['is_free'] ?? false)
            || (int) ($this->attributes['price'] ?? $this->price ?? 0) <= 0;
    }

    public function effectiveAttemptLimit(): int
    {
        $attemptLimit = (int) ($this->attributes['attempt_limit'] ?? $this->attempt_limit ?? 0);

        if ($attemptLimit > 0) {
            return $attemptLimit;
        }

        return $this->isFreePackage() ? 1 : 3;
    }

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

    public function scopeOfTryoutType(Builder $query, ?string $type): Builder
    {
        return $query->where('tryout_type', TryoutBlueprint::normalizeType($type));
    }

    public function checkoutProduct()
    {
        if ($this->isFreePackage()) {
            return null;
        }

        $description = trim((string) ($this->description ?: 'Produk akses untuk membuka paket tryout Ruang Cerdas.'));
        $shortDescription = $this->checkoutShortDescription();

        return Product::withTrashed()->updateOrCreate(
            ['slug' => $this->slug],
            [
                'product_type' => 'tryout',
                'category' => $this->tryout_type,
                'name' => $this->title,
                'short_description' => $shortDescription,
                'description' => $description,
                'normal_price' => max(0, (int) $this->price),
                'sale_price' => max(0, (int) $this->price),
                'is_active' => (bool) $this->is_active,
                'published_at' => now(),
                'deleted_at' => null,
            ]
        );
    }

    public function checkoutShortDescription(): string
    {
        $typeLabel = TryoutBlueprint::typeLabel($this->tryout_type);
        $sectionLabel = collect($this->sectionSummaries())->pluck('label')->filter()->implode(', ');
        $positionLabel = $this->position_target_label;
        $totalQuestions = $this->total_questions;
        $durationMinutes = (int) $this->duration_minutes;
        $accessLabel = $this->access_days ? $this->access_days . ' hari' : 'tanpa batas hari';
        $attemptLabel = $this->effectiveAttemptLimit() . 'x percobaan';

        $parts = collect([
            $sectionLabel !== '' ? 'Akses ' . $typeLabel . ' untuk ' . $sectionLabel : 'Akses ' . $typeLabel,
            $positionLabel ? 'target ' . $positionLabel : null,
            $totalQuestions > 0 ? $totalQuestions . ' soal' : null,
            $durationMinutes > 0 ? $durationMinutes . ' menit' : null,
            'akses ' . $accessLabel,
            $attemptLabel,
        ])->filter()->values();

        return Str::limit($parts->implode(' · '), 250, '...');
    }

    public function getTotalQuestionsAttribute(): int
    {
        return collect($this->sectionSummaries())->sum('count');
    }

    public function getTryoutTypeLabelAttribute(): string
    {
        return TryoutBlueprint::typeLabel($this->tryout_type);
    }

    public function getPositionTargetLabelAttribute(): ?string
    {
        return TryoutBlueprint::positionLabel($this->tryout_type, $this->position_target);
    }

    public function routeSegment(): string
    {
        return TryoutBlueprint::routeSegment($this->tryout_type);
    }

    public function listingRouteName(): string
    {
        return match (TryoutBlueprint::normalizeType($this->tryout_type)) {
            TryoutBlueprint::TYPE_PPPK => 'public.tryouts.pppk',
            TryoutBlueprint::TYPE_PPPK_TENDIK => 'public.tryouts.pppk-tendik',
            default => 'public.tryouts.index',
        };
    }

    public function cardDisplayTitle(): string
    {
        $title = trim(preg_replace('/\s+/u', ' ', (string) $this->title));

        if (TryoutBlueprint::normalizeType($this->tryout_type) !== TryoutBlueprint::TYPE_PPPK_TENDIK) {
            return $title;
        }

        $title = preg_replace('/\s*-\s*.+$/u', '', $title);
        $title = preg_replace('/\bPPPK\s+Tendik\b/iu', '', (string) $title);
        $title = preg_replace('/\b20\d{2}\b/u', '', (string) $title);
        $title = trim(preg_replace('/\s+/u', ' ', (string) $title), " -");

        return $title !== '' ? $title : trim((string) $this->title);
    }

    public function cardDisplaySubtitle(): ?string
    {
        $title = trim((string) $this->title);

        if (preg_match('/-\s*(.+)$/u', $title, $matches)) {
            return trim($matches[1]);
        }

        return $this->position_target_label;
    }

    public function sectionSummaries(): array
    {
        $sections = collect($this->rawSectionComposition());

        $thresholds = $this->sectionThresholds();

        return $sections
            ->map(function (array $section) use ($thresholds) {
                $key = (string) ($section['key'] ?? '');

                return [
                    'key' => $key,
                    'label' => $section['label'] ?? TryoutBlueprint::sectionLabel($this->tryout_type, $key),
                    'count' => (int) ($section['count'] ?? 0),
                    'scoring_mode' => $section['scoring_mode'] ?? TryoutBlueprint::scoringMode($this->tryout_type, $key),
                    'threshold' => $thresholds[$key] ?? null,
                ];
            })
            ->filter(fn (array $section) => $section['count'] > 0)
            ->values()
            ->all();
    }

    public function sectionKeys(): array
    {
        return collect($this->sectionSummaries())->pluck('key')->all();
    }

    public function sectionThresholds(): array
    {
        $baseThresholds = TryoutBlueprint::defaultThresholds($this->tryout_type);
        $storedThresholds = $this->section_thresholds ?: [];
        $composition = $this->rawSectionComposition();

        if ($storedThresholds === []) {
            return TryoutBlueprint::scaledThresholds($this->tryout_type, $composition);
        }

        if (
            TryoutBlueprint::normalizeType($this->tryout_type) === TryoutBlueprint::TYPE_CPNS
            && $storedThresholds == $baseThresholds
        ) {
            return TryoutBlueprint::scaledThresholds($this->tryout_type, $composition);
        }

        return $storedThresholds;
    }

    private function rawSectionComposition(): array
    {
        $sections = collect($this->section_composition);

        if ($sections->isEmpty()) {
            $sections = collect([
                ['key' => 'twk', 'label' => 'TWK', 'count' => (int) $this->twk_count, 'scoring_mode' => 'single_correct'],
                ['key' => 'tiu', 'label' => 'TIU', 'count' => (int) $this->tiu_count, 'scoring_mode' => 'single_correct'],
                ['key' => 'tkp', 'label' => 'TKP', 'count' => (int) $this->tkp_count, 'scoring_mode' => 'weighted'],
            ]);
        }

        return $sections->all();
    }

    public function totalThreshold(): ?int
    {
        $thresholds = $this->sectionThresholds();

        return isset($thresholds['total']) ? (int) $thresholds['total'] : null;
    }

    public function usesScaledCpnsThresholds(): bool
    {
        if (TryoutBlueprint::normalizeType($this->tryout_type) !== TryoutBlueprint::TYPE_CPNS) {
            return false;
        }

        $baseThresholds = TryoutBlueprint::defaultThresholds($this->tryout_type);
        $scaledThresholds = TryoutBlueprint::scaledThresholds($this->tryout_type, $this->rawSectionComposition());

        return $this->sectionThresholds() === $scaledThresholds
            && $scaledThresholds !== $baseThresholds;
    }

    public function thresholdLabel(): string
    {
        if (TryoutBlueprint::normalizeType($this->tryout_type) !== TryoutBlueprint::TYPE_CPNS) {
            return 'Ambang paket tryout';
        }

        return $this->usesScaledCpnsThresholds()
            ? 'Ambang paket latihan'
            : 'Ambang simulasi SKD';
    }

    public function thresholdSummaryLine(): ?string
    {
        $items = collect($this->sectionSummaries())
            ->map(function (array $section) {
                if ($section['threshold'] === null) {
                    return null;
                }

                return trim($section['label'] . ' ' . (int) $section['threshold']);
            })
            ->filter()
            ->values();

        if ($this->totalThreshold() !== null) {
            $items->push('Total ' . $this->totalThreshold());
        }

        return $items->isNotEmpty() ? $items->implode(' · ') : null;
    }

    public function setTryoutTypeAttribute($value): void
    {
        $this->attributes['tryout_type'] = TryoutBlueprint::normalizeType($value);
    }

    public function setPositionTargetAttribute($value): void
    {
        $this->attributes['position_target'] = TryoutBlueprint::normalizePositionTarget(
            $this->attributes['tryout_type'] ?? $this->tryout_type ?? null,
            $value
        );
    }
}
