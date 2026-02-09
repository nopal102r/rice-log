<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // regular, land_management
        'weight',
        'box_count', // for land management
        'money_amount', // for sales
        'wage_amount', // calculated wage
        'price_per_kg', // context dependent
        'total_price', // context dependent or unused for some
        'photo',
        'notes',
        'status',
        'start_time',
        'end_time',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'weight' => 'decimal:2',
        'box_count' => 'integer',
        'money_amount' => 'decimal:2',
        'wage_amount' => 'decimal:2',
    ];

    /**
     * Get the user associated with the deposit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who verified the deposit.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get total deposits for a user in current month
     */
    public static function getTotalMonthDeposits($userId, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $deposits = self::where('user_id', $userId)
            ->where('status', 'verified')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        return [
            'total_kg' => $deposits->sum('weight'),
            'total_wage' => $deposits->sum('wage_amount'), // Changed from total_price to wage_amount for salary
            'total_revenue' => $deposits->sum('money_amount'), // Added for sales tracking
            'count' => $deposits->count(),
        ];
    }

    /**
     * Get last 7 days deposits for a user
     */
    public static function getLastWeekDeposits($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', 'verified')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();
    }
}
