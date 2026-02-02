<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_per_kg',
        'office_latitude',
        'office_longitude',
        'max_distance_allowed',
        'leave_days_per_month',
        'min_deposit_per_week',
    ];

    protected $casts = [
        'price_per_kg' => 'float',
        'office_latitude' => 'float',
        'office_longitude' => 'float',
        'max_distance_allowed' => 'float',
    ];

    /**
     * Get the current payroll settings
     */
    public static function getCurrent()
    {
        return self::first() ?? self::createDefault();
    }

    /**
     * Create default payroll settings
     */
    public static function createDefault()
    {
        return self::create([
            'price_per_kg' => 30000, // Default 30.000 rupiah per kg
            'office_latitude' => -6.2088, // Default Jakarta
            'office_longitude' => 106.8456,
            'max_distance_allowed' => 2.0,
            'leave_days_per_month' => 3,
            'min_deposit_per_week' => 1,
        ]);
    }
}
