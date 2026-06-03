<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TryoutAnswer extends Model
{
    protected $fillable = [
        'tryout_session_id',
        'question_id',
        'question_option_id',
        'is_marked',
        'score',
    ];

    protected $casts = [
        'is_marked' => 'boolean',
        'score' => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TryoutSession::class, 'tryout_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
