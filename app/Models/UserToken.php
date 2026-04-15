<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'status',
        'used_at_absence_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(FlexibilityItem::class, 'item_id');
    }

    public function usedAtAbsence(): BelongsTo
    {
        return $this->belongsTo(Absence::class, 'used_at_absence_id');
    }
}
