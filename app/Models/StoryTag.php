<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'user_id',
        'name',
        'dx',
        'dy',
        'text_options',
    ];

    protected $casts = [
        'text_options' => 'array',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
