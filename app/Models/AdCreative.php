<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdCreative extends Model
{
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
}
