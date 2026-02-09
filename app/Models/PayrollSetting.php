<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_per_kg',
        'driver_rate_per_kg',
        'farmer_rate_per_box',
        'miller_rate_per_kg',
        'sales_commission_rate',
        'office_latitude',
        'office_longitude',
        'max_distance_allowed',
        'leave_days_per_month',
        'min_deposit_per_week',
        'packing_rate_per_kg',
    ];

    protected $casts = [
        'price_per_kg' => 'float',
        'driver_rate_per_kg' => 'float',
        'farmer_rate_per_box' => 'float',
        'miller_rate_per_kg' => 'float',
        'sales_commission_rate' => 'float',
        'packing_rate_per_kg' => 'float',
        'office_latitude' => 'float',
        'office_longitude' => 'float',
        'max_distance_allowed' => 'float',
    ];

    /**
     * Get the current payroll settings
     */
    public static function getCurrent()
    {
        // Always fetch fresh from DB to avoid stale data issues
        return self::latest()->first() ?? self::createDefault();
    }

    /**
     * Create default payroll settings
     */
    public static function createDefault()
    {
        return self::create([
            'price_per_kg' => 30000, 
            'driver_rate_per_kg' => 500, // Default driver rate
            'farmer_rate_per_box' => 50000, // Default farmer box rate
            'miller_rate_per_kg' => 200, // Default miller rate
            'sales_commission_rate' => 5.0, // Default 5% commission
            'office_latitude' => -6.2088, 
            'office_longitude' => 106.8456,
            'max_distance_allowed' => 2.0,
            'leave_days_per_month' => 3,
            'min_deposit_per_week' => 1,
            'packing_rate_per_kg' => 20, // Default packing rate per kg
        ]);
    }
}
