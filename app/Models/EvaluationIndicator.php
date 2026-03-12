<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationIndicator extends Model
{
    // Kolom yang bisa diisi: nama kategori (misal: Disiplin)
    protected $fillable = ['name'];

    /**
     * Relasi ke EvaluationDescription.
     * Satu kategori (Indikator) punya banyak butir pertanyaan (Deskripsi).
     */
    public function descriptions(): HasMany
    {
        return $this->hasMany(EvaluationDescription::class);
    }
}
