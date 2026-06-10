<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'product_type',
        'category',
        'name',
        'slug',
        'short_description',
        'description',
        'benefits',
        'contents',
        'normal_price',
        'sale_price',
        'first_buyer_price',
        'first_buyer_quota',
        'cover_image',
        'digital_file_path',
        'download_filename',
        'file_size',
        'file_mime_type',
        'file_uploaded_at',
        'is_featured',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'normal_price' => 'integer',
        'sale_price' => 'integer',
        'first_buyer_price' => 'integer',
        'first_buyer_quota' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'file_uploaded_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public const PRODUCT_TYPE_LABELS = [
        'tryout' => 'Tryout',
        'ebook' => 'eBook',
        'template' => 'Template',
        'source_code' => 'Source Code',
        'bundle' => 'Bundle',
    ];

    public function getAttribute($key)
    {
        if ($key === 'category') {
            if ($this->relationLoaded('category')) {
                return $this->relations['category'];
            }

            if ($this->getAttributeFromArray('category_id')) {
                return $this->getRelationValue('category') ?? $this->getAttributeFromArray('category');
            }
        }

        return parent::getAttribute($key);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function previewImages(): HasMany
    {
        return $this->hasMany(ProductPreviewImage::class);
    }

    public function adCreatives(): HasMany
    {
        return $this->hasMany(AdCreative::class);
    }

    public function viewEvents(): HasMany
    {
        return $this->hasMany(ProductViewEvent::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePublicVisible(Builder $query): Builder
    {
        return $query->visibleToPublic();
    }

    public function scopeVisibleToPublic(Builder $query): Builder
    {
        return $query
            ->active()
            ->published()
            ->whereNotNull('digital_file_path');
    }

    public function scopeOfProductType(Builder $query, ?string $type): Builder
    {
        if (blank($type) || ! array_key_exists($type, self::PRODUCT_TYPE_LABELS)) {
            return $query;
        }

        return $query->where('product_type', $type);
    }

    public static function productTypeLabels(): array
    {
        return self::PRODUCT_TYPE_LABELS;
    }

    public function getPublicPriceAttribute(): int
    {
        return $this->sale_price ?: $this->normal_price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return !empty($this->sale_price) && $this->sale_price < $this->normal_price;
    }

    public function getHasFileAttribute(): bool
    {
        return filled($this->digital_file_path);
    }

    public function hasPrivateFile(): bool
    {
        return filled($this->digital_file_path);
    }

    public function privateFileExists(): bool
    {
        return $this->hasPrivateFile()
            && Storage::disk('private')->exists($this->digital_file_path);
    }

    public function isMissingPrivateFile(): bool
    {
        return ! $this->privateFileExists();
    }

    public function isVisibleToPublic(): bool
    {
        return (bool) $this->is_active
            && filled($this->published_at)
            && $this->published_at->lte(now())
            && $this->privateFileExists();
    }

    public function getFormattedFileSizeAttribute(): ?string
    {
        if (empty($this->file_size) || $this->file_size < 1) {
            return null;
        }

        $bytes = (float) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return number_format($bytes / (1024 ** $power), 2, ',', '.') . ' ' . $units[$power];
    }
}
