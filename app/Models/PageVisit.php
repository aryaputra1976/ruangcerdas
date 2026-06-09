<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = [
        'route_name',
        'path',
        'url',
        'visitor_hash',
        'ip_hash',
        'user_agent',
        'referer',
        'visited_on',
        'visited_at',
    ];

    protected $casts = [
        'visited_on' => 'date',
        'visited_at' => 'datetime',
    ];
}
