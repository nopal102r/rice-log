<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the leave.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the leave.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate total days for the leave submission
     */
    public function getTotalDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Get pending leave submissions
     */
    public static function getPendingSubmissions()
    {
        return self::where('status', 'pending')
            ->with(['user'])
            ->latest()
            ->get();
    }

    /**
     * Get leave submissions for a specific user in a month
     */
    public static function getMonthLeaves($userId, $month, $year)
    {
        return self::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->get();
    }

    /**
     * Get total approved leave days for user in a month
     */
    public static function getTotalApprovedDaysInMonth($userId, $month, $year)
    {
        $leaves = self::getMonthLeaves($userId, $month, $year);
        $totalDays = 0;

        foreach ($leaves as $leave) {
            $totalDays += $leave->getTotalDays();
        }

        return $totalDays;
    }
}
