<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'notifiable_type',
        'notifiable_id',
        'read',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'read' => 'boolean',
    ];

    /**
     * Get the user associated with the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notifiable model.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Get unread notifications for a user
     */
    public static function getUnreadForUser($userId)
    {
        return self::where('user_id', $userId)
            ->where('read', false)
            ->latest()
            ->get();
    }

    /**
     * Count unread notifications for a user
     */
    public static function countUnreadForUser($userId): int
    {
        return self::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }
}
