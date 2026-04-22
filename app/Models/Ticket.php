<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'category_id', 
        'operator_id', 
        'subject', 
        'description', 
        'status', 
        'rating', 
        'first_replied_at', 
        'resolved_at'
    ];

    protected $casts = [
        'first_replied_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    // Scopes
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    // SLA Methods
    public function getResponseTimeInMinutes(): ?int
    {
        if (!$this->first_replied_at) return null;
        return $this->created_at->diffInMinutes($this->first_replied_at);
    }

    public function getResolutionTimeInMinutes(): ?int
    {
        if (!$this->resolved_at) return null;
        return $this->created_at->diffInMinutes($this->resolved_at);
    }
}
