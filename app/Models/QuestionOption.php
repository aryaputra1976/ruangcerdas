<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_label',
        'option_text',
        'is_correct',
        'score',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function tryoutAnswers(): HasMany
    {
        return $this->hasMany(TryoutAnswer::class);
    }
}
