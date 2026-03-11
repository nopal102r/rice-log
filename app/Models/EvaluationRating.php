<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationRating extends Model
{
    protected $fillable = [
        'evaluation_id',
        'evaluation_description_id',
        'rating',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function description(): BelongsTo
    {
        return $this->belongsTo(EvaluationDescription::class, 'evaluation_description_id');
    }
}
