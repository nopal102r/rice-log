<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationDescription extends Model
{
    // Kolom yang bisa diisi: ID Indikator induk dan teks pertanyaannya
    protected $fillable = ['evaluation_indicator_id', 'name'];

    /**
     * Relasi balik ke model EvaluationIndicator.
     * Butir pertanyaan ini termasuk ke dalam kategori (Indikator) tertentu.
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(EvaluationIndicator::class, 'evaluation_indicator_id');
    }
}
