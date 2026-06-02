<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadMagnet extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_path',
        'cover_image',
        'is_active',
        'download_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'download_count' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(LeadSubscriber::class);
    }
}
