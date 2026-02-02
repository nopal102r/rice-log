<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'days_present',
        'days_sick',
        'days_leave',
        'leave_approved',
        'total_kg_deposited',
        'total_salary',
        'status',
    ];

    /**
     * Get the user associated with the monthly summary.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create summary for current month
     */
    public static function getOrCreateCurrent($userId, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return self::firstOrCreate(
            [
                'user_id' => $userId,
                'year' => $year,
                'month' => $month,
            ],
            [
                'days_present' => 0,
                'days_sick' => 0,
                'days_leave' => 0,
                'leave_approved' => 0,
                'total_kg_deposited' => 0,
                'total_salary' => 0,
            ]
        );
    }

    /**
     * Calculate and update summary data
     */
    public function calculateSummary(): void
    {
        // Count absences
        $absences = Absence::where('user_id', $this->user_id)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->get();

        $this->days_present = $absences->where('status', 'hadir')->count();
        $this->days_sick = $absences->where('status', 'sakit')->count();
        $this->days_leave = $absences->where('status', 'izin')->count();

        // Count approved leave days
        $this->leave_approved = LeaveSubmission::getTotalApprovedDaysInMonth(
            $this->user_id,
            $this->month,
            $this->year
        );

        // Count total deposits
        $deposits = Deposit::getTotalMonthDeposits($this->user_id, $this->month, $this->year);
        $this->total_kg_deposited = $deposits['total_kg'];
        $this->total_salary = $deposits['total_price'];

        // Determine if employee was active (had at least one presence)
        $this->status = $this->days_present > 0 ? 'active' : 'inactive';

        $this->save();
    }
}
