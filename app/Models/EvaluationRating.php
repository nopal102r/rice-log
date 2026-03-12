<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationRating extends Model
{
    // Kolom yang menyimpan detail nilai: ID Raport, ID Pertanyaan, dan Skornya
    protected $fillable = [
        'evaluation_id',
        'evaluation_description_id',
        'rating',
    ];

    /**
     * Relasi balik ke model Evaluation (Header Raport).
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Relasi ke model EvaluationDescription (Pertanyaan yang dinilai).
     */
    public function description(): BelongsTo
    {
        return $this->belongsTo(EvaluationDescription::class, 'evaluation_description_id');
    }
}
