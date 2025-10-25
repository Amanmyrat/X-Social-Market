<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referee_id',
        'reward_amount',
        'reward_claimed',
        'reward_claimed_at',
        'status',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'reward_claimed' => 'boolean',
        'reward_claimed_at' => 'datetime',
    ];

    /**
     * Get the user who referred (the referrer).
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the user who was referred (the referee).
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }
}

