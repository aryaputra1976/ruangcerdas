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
                ['key' => 'sosiokultural', 'label' => 'Sosiokultural', 'scoring_mode' => 'weighted', 'threshold' => null],
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
                ['key' => 'sosiokultural', 'label' => 'Sosiokultural', 'scoring_mode' => 'weighted', 'threshold' => null],
                ['key' => 'wawancara', 'label' => 'Wawancara', 'scoring_mode' => 'weighted', 'threshold' => null],
            ],
            'total_threshold' => null,
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
}
