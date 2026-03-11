<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationDescription extends Model
{
    protected $fillable = ['evaluation_indicator_id', 'name'];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(EvaluationIndicator::class, 'evaluation_indicator_id');
    }
}
