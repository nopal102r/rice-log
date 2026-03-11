<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $fillable = [
        'user_id',
        'boss_id',
        'month',
        'year',
        'feedback',
        'bonus',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function boss(): BelongsTo
    {
        return $this->belongsTo(User::class, 'boss_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(EvaluationRating::class);
    }
}
