<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'unit',
    ];

    /**
     * Get stock by name
     */
    public static function getByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    /**
     * Update stock quantity
     */
    public function updateQuantity(float $amount): bool
    {
        $this->quantity += $amount;
        return $this->save();
    }

    /**
     * Check if stock is available
     */
    public function isAvailable(float $requiredAmount): bool
    {
        return $this->quantity >= $requiredAmount;
    }

    /**
     * Decrement stock
     */
    public function decrementStock(float $amount): bool
    {
        if (!$this->isAvailable($amount)) {
            return false;
        }
        $this->quantity -= $amount;
        return $this->save();
    }

    /**
     * Increment stock
     */
    public function incrementStock(float $amount): bool
    {
        $this->quantity += $amount;
        return $this->save();
    }
}
