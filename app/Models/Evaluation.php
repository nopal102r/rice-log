<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    // Daftar kolom yang diperbolehkan untuk diisi secara massal
    protected $fillable = [
        'user_id',   // ID Karyawan yang dinilai
        'boss_id',   // ID Atasan yang menilai
        'month',     // Bulan penilaian (1-12)
        'year',      // Tahun penilaian
        'feedback',  // Catatan/Feedback dari atasan
        'bonus',     // Nominal bonus yang diberikan
    ];

    /**
     * Relasi ke model User (Karyawan).
     * Satu evaluasi dimiliki oleh satu karyawan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model User (Boss).
     * Satu evaluasi dicatat oleh satu atasan.
     */
    public function boss(): BelongsTo
    {
        return $this->belongsTo(User::class, 'boss_id');
    }

    /**
     * Relasi ke model EvaluationRating.
     * Satu raport evaluasi punya banyak rincian nilai per butir pertanyaan.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(EvaluationRating::class);
    }
}
