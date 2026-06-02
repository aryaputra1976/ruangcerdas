<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadSubscriber extends Model
{
    protected $fillable = [
        'lead_magnet_id',
        'name',
        'email',
        'whatsapp',
        'ip_address',
        'user_agent',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function leadMagnet(): BelongsTo
    {
        return $this->belongsTo(LeadMagnet::class);
    }
}
