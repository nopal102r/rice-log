<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlexibilityItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'point_cost',
        'tolerance_minutes',
        'stock_limit',
    ];

    public function userTokens(): HasMany
    {
        return $this->hasMany(UserToken::class, 'item_id');
    }
}
