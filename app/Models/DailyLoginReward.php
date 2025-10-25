<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLoginReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'highest_streak',
        'last_login_date',
        'total_earned',
        'total_claims',
    ];

    protected $casts = [
        'last_login_date' => 'date',
        'total_earned' => 'decimal:2',
    ];

    /**
     * Get the user that owns the daily login reward.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

