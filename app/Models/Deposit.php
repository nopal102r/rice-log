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
        'weight',
        'price_per_kg',
        'total_price',
        'photo',
        'notes',
        'status',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
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
            'total_price' => $deposits->sum('total_price'),
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

    /**
     * Calculate total price based on weight and price per kg
     */
    public function calculateTotalPrice(): void
    {
        $this->total_price = $this->weight * $this->price_per_kg;
    }
}
