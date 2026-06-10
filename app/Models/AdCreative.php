<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdCreative extends Model
{
    public const TEMPLATES = [
        'viral_note' => [
            'label' => 'Viral Note 9:16',
            'preview_label' => 'Catatan Penting',
            'visual_direction' => 'Gaya catatan edukatif dengan nuansa lembut dan cocok untuk soft selling.',
            'palette' => [
                'cream' => [247, 238, 223],
                'paper' => [255, 251, 245],
                'ink' => [39, 35, 31],
                'muted' => [108, 101, 92],
                'accent' => [193, 43, 43],
                'line' => [224, 208, 187],
            ],
        ],
        'urgent_offer' => [
            'label' => 'Urgent Offer 9:16',
            'preview_label' => 'Jangan Lewatkan',
            'visual_direction' => 'Warna promo lebih kuat untuk diskon, kuota, atau penawaran terbatas.',
            'palette' => [
                'cream' => [248, 236, 225],
                'paper' => [255, 249, 243],
                'ink' => [44, 31, 24],
                'muted' => [120, 93, 78],
                'accent' => [175, 34, 34],
                'line' => [227, 202, 182],
            ],
        ],
        'social_proof' => [
            'label' => 'Social Proof 9:16',
            'preview_label' => 'Kenapa Banyak Yang Suka',
            'visual_direction' => 'Cocok untuk membangun trust, manfaat praktis, dan gaya konten viral.',
            'palette' => [
                'cream' => [243, 237, 225],
                'paper' => [253, 250, 245],
                'ink' => [34, 38, 41],
                'muted' => [90, 94, 102],
                'accent' => [184, 55, 55],
                'line' => [215, 206, 190],
            ],
        ],
    ];

    public const SIZE_PRESETS = [
        'story' => ['label' => 'Story 9:16', 'width' => 1080, 'height' => 1920],
        'feed_portrait' => ['label' => 'Feed Portrait 4:5', 'width' => 1080, 'height' => 1350],
        'square' => ['label' => 'Square 1:1', 'width' => 1080, 'height' => 1080],
    ];

    protected $fillable = [
        'product_id',
        'template_key',
        'title',
        'headline',
        'body',
        'bullets',
        'cta_text',
        'brand_text',
        'image_path',
        'format',
        'width',
        'height',
        'created_by',
    ];

    protected $casts = [
        'bullets' => 'array',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function sizePresetOptions(): array
    {
        return collect(self::SIZE_PRESETS)
            ->mapWithKeys(fn (array $preset, string $key) => [$key => $preset['label']])
            ->all();
    }

    public static function templateOptions(): array
    {
        return collect(self::TEMPLATES)
            ->mapWithKeys(fn (array $template, string $key) => [$key => $template['label']])
            ->all();
    }

    public static function templateDefinitions(): array
    {
        return self::TEMPLATES;
    }

    public static function sizePresetLabel(string $key): string
    {
        return self::SIZE_PRESETS[$key]['label'] ?? $key;
    }

    public static function sizePresetKeyFromDimensions(int $width, int $height): ?string
    {
        foreach (self::SIZE_PRESETS as $key => $preset) {
            if ((int) $preset['width'] === $width && (int) $preset['height'] === $height) {
                return $key;
            }
        }

        return null;
    }
}
