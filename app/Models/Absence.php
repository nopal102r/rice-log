<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'description',
        'face_image',
        'latitude',
        'longitude',
        'distance_from_office',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    /**
     * Get the user associated with the absence.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get today's absences for a specific user
     */
    public static function getTodayAbsenceForUser($userId, $type)
    {
        return self::where('user_id', $userId)
            ->where('type', $type)
            ->whereDate('created_at', today())
            ->first();
    }

    /**
     * Check if user already checked in today
     */
    public static function hasCheckedInToday($userId)
    {
        return self::where('user_id', $userId)
            ->where('type', 'masuk')
            ->whereDate('created_at', today())
            ->exists();
    }

    /**
     * Check if user already checked out today
     */
    public static function hasCheckedOutToday($userId)
    {
        return self::where('user_id', $userId)
            ->where('type', 'keluar')
            ->whereDate('created_at', today())
            ->exists();
    }
}
