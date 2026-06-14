<?php

namespace App\Support;

class TryoutBlueprint
{
    public const TYPE_CPNS = 'cpns';
    public const TYPE_PPPK = 'pppk';
    public const TYPE_PPPK_TENDIK = 'pppk_tendik';

    private const TYPES = [
        self::TYPE_CPNS => [
            'label' => 'Tryout CPNS',
            'route_segment' => 'cpns',
            'sections' => [
                ['key' => 'twk', 'label' => 'TWK', 'scoring_mode' => 'single_correct', 'threshold' => 65],
                ['key' => 'tiu', 'label' => 'TIU', 'scoring_mode' => 'single_correct', 'threshold' => 80],
                ['key' => 'tkp', 'label' => 'TKP', 'scoring_mode' => 'weighted', 'threshold' => 166],
            ],
            'total_threshold' => 311,
        ],
        self::TYPE_PPPK => [
            'label' => 'Tryout PPPK',
            'route_segment' => 'pppk',
            'sections' => [
                ['key' => 'teknis', 'label' => 'Kompetensi Teknis', 'scoring_mode' => 'single_correct', 'threshold' => null],
                ['key' => 'manajerial', 'label' => 'Manajerial', 'scoring_mode' => 'weighted', 'threshold' => null],
                ['key' => 'sosiokultural', 'label' => 'Sosial Kultural', 'scoring_mode' => 'weighted', 'threshold' => null],
                ['key' => 'wawancara', 'label' => 'Wawancara', 'scoring_mode' => 'weighted', 'threshold' => null],
            ],
            'total_threshold' => null,
        ],
        self::TYPE_PPPK_TENDIK => [
            'label' => 'Tryout PPPK Tendik',
            'route_segment' => 'pppk-tendik',
            'sections' => [
                ['key' => 'teknis', 'label' => 'Kompetensi Teknis', 'scoring_mode' => 'single_correct', 'threshold' => null],
                ['key' => 'manajerial', 'label' => 'Manajerial', 'scoring_mode' => 'weighted', 'threshold' => null],
                ['key' => 'sosiokultural', 'label' => 'Sosial Kultural', 'scoring_mode' => 'weighted', 'threshold' => null],
                ['key' => 'wawancara', 'label' => 'Wawancara', 'scoring_mode' => 'weighted', 'threshold' => null],
            ],
            'total_threshold' => null,
        ],
    ];

    private const CPNS_BASE_SECTION_MAX_SCORES = [
        'twk' => 150,
        'tiu' => 175,
        'tkp' => 225,
    ];

    private const CPNS_BASE_TOTAL_MAX_SCORE = 550;
    private const DEFAULT_OPTION_LABELS = ['A', 'B', 'C', 'D', 'E'];
    private const FOUR_OPTION_LABELS = ['A', 'B', 'C', 'D'];
    private const POSITION_OPTIONS = [
        self::TYPE_PPPK_TENDIK => [
            'wali_asuh' => 'Wali Asuh',
            'wali_asrama' => 'Wali Asrama',
            'operator_sekolah' => 'Operator Sekolah',
            'pengelola_keuangan' => 'Pengelola Keuangan',
            'tenaga_administrasi' => 'Tenaga Administrasi',
        ],
    ];

    public static function typeOptions(): array
    {
        return collect(self::TYPES)
            ->mapWithKeys(fn (array $config, string $type) => [$type => $config['label']])
            ->all();
    }

    public static function normalizeType(?string $type): string
    {
        return array_key_exists((string) $type, self::TYPES) ? (string) $type : self::TYPE_CPNS;
    }

    public static function typeLabel(?string $type): string
    {
        $type = self::normalizeType($type);

        return self::TYPES[$type]['label'];
    }

    public static function routeSegment(?string $type): string
    {
        $type = self::normalizeType($type);

        return self::TYPES[$type]['route_segment'];
    }

    public static function routeSegmentToType(?string $segment): string
    {
        foreach (self::TYPES as $type => $config) {
            if ($config['route_segment'] === $segment) {
                return $type;
            }
        }

        return self::TYPE_CPNS;
    }

    public static function sections(?string $type): array
    {
        $type = self::normalizeType($type);

        return self::TYPES[$type]['sections'];
    }

    public static function sectionOptions(?string $type): array
    {
        return collect(self::sections($type))
            ->mapWithKeys(fn (array $section) => [$section['key'] => $section['label']])
            ->all();
    }

    public static function supportsPositionTarget(?string $type): bool
    {
        $type = self::normalizeType($type);

        return array_key_exists($type, self::POSITION_OPTIONS);
    }

    public static function requiresPositionTarget(?string $type): bool
    {
        return self::supportsPositionTarget($type);
    }

    public static function positionOptions(?string $type): array
    {
        $type = self::normalizeType($type);

        return self::POSITION_OPTIONS[$type] ?? [];
    }

    public static function normalizePositionTarget(?string $type, mixed $positionTarget): ?string
    {
        $type = self::normalizeType($type);
        $value = filled($positionTarget) ? strtolower(trim((string) $positionTarget)) : null;

        if (! self::supportsPositionTarget($type) || blank($value)) {
            return null;
        }

        return array_key_exists($value, self::positionOptions($type)) ? $value : null;
    }

    public static function positionLabel(?string $type, mixed $positionTarget): ?string
    {
        $normalized = self::normalizePositionTarget($type, $positionTarget);

        if ($normalized === null) {
            return null;
        }

        return self::positionOptions($type)[$normalized] ?? null;
    }

    public static function sectionLabel(?string $type, ?string $sectionKey): string
    {
        $sectionKey = strtolower((string) $sectionKey);
        $section = collect(self::sections($type))->firstWhere('key', $sectionKey);

        return $section['label'] ?? strtoupper(str_replace('_', ' ', $sectionKey));
    }

    public static function scoringMode(?string $type, ?string $sectionKey): string
    {
        $sectionKey = strtolower((string) $sectionKey);
        $section = collect(self::sections($type))->firstWhere('key', $sectionKey);

        return $section['scoring_mode'] ?? 'single_correct';
    }

    public static function scoringRuleLabel(?string $type, ?string $sectionKey): string
    {
        return self::scoringMode($type, $sectionKey) === 'weighted'
            ? 'Skor bertingkat ' . implode(',', range(self::maxWeightedScore($type, $sectionKey), 1))
            : 'Benar 5, salah 0';
    }

    public static function optionLabels(?string $type, ?string $sectionKey): array
    {
        $type = self::normalizeType($type);
        $sectionKey = strtolower((string) $sectionKey);

        if (
            $type === self::TYPE_PPPK_TENDIK
            && in_array($sectionKey, ['manajerial', 'sosiokultural', 'wawancara'], true)
        ) {
            return self::FOUR_OPTION_LABELS;
        }

        return self::DEFAULT_OPTION_LABELS;
    }

    public static function requiredOptionCount(?string $type, ?string $sectionKey): int
    {
        return count(self::optionLabels($type, $sectionKey));
    }

    public static function maxWeightedScore(?string $type, ?string $sectionKey): int
    {
        return self::requiredOptionCount($type, $sectionKey);
    }

    public static function isValidSection(?string $type, ?string $sectionKey): bool
    {
        return array_key_exists(strtolower((string) $sectionKey), self::sectionOptions($type));
    }

    public static function defaultSectionComposition(?string $type): array
    {
        return collect(self::sections($type))
            ->map(fn (array $section) => [
                'key' => $section['key'],
                'label' => $section['label'],
                'count' => 0,
                'scoring_mode' => $section['scoring_mode'],
            ])
            ->values()
            ->all();
    }

    public static function defaultThresholds(?string $type): array
    {
        $thresholds = [];

        foreach (self::sections($type) as $section) {
            if ($section['threshold'] !== null) {
                $thresholds[$section['key']] = $section['threshold'];
            }
        }

        $type = self::normalizeType($type);
        $totalThreshold = self::TYPES[$type]['total_threshold'];

        if ($totalThreshold !== null) {
            $thresholds['total'] = $totalThreshold;
        }

        return $thresholds;
    }

    public static function scaledThresholds(?string $type, array $sectionComposition = []): array
    {
        $type = self::normalizeType($type);
        $thresholds = self::defaultThresholds($type);

        if ($type !== self::TYPE_CPNS) {
            return $thresholds;
        }

        $compositionByKey = collect($sectionComposition)
            ->mapWithKeys(fn (array $section) => [strtolower((string) ($section['key'] ?? '')) => (int) ($section['count'] ?? 0)])
            ->all();

        $scaled = [];
        $totalMaxScore = 0;

        foreach (self::sections($type) as $section) {
            $key = $section['key'];
            $questionCount = (int) ($compositionByKey[$key] ?? 0);

            if ($questionCount < 1) {
                continue;
            }

            $sectionMaxScore = $questionCount * 5;
            $totalMaxScore += $sectionMaxScore;

            if (! isset($thresholds[$key])) {
                continue;
            }

            $baseMaxScore = self::CPNS_BASE_SECTION_MAX_SCORES[$key] ?? 0;

            if ($baseMaxScore < 1) {
                $scaled[$key] = (int) $thresholds[$key];

                continue;
            }

            $scaled[$key] = min(
                $sectionMaxScore,
                (int) max(0, round(((int) $thresholds[$key] / $baseMaxScore) * $sectionMaxScore))
            );
        }

        if (isset($thresholds['total']) && $totalMaxScore > 0) {
            $scaled['total'] = min(
                $totalMaxScore,
                (int) max(0, round(((int) $thresholds['total'] / self::CPNS_BASE_TOTAL_MAX_SCORE) * $totalMaxScore))
            );
        }

        return $scaled;
    }
}
