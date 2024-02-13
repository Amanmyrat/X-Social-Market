<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Story
 *
 * @mixin Eloquent
 *
 * @property mixed id
 * @property mixed user_id
 * @property mixed post_id
 * @property string image
 * @property mixed valid_until
 */
class Story extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'image',
        'valid_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'valid_until',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_until' => 'datetime',
    ];

    /**
     * Get the user that owns the story.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class)->with('media');
    }

    public function views(): HasMany
    {
        return $this->hasMany(StoryView::class)->with('user');
    }

    public function myViews(): HasMany
    {
        return $this->hasMany(StoryView::class)
            ->where('user_id', auth('sanctum')->user()->id);
    }

    public function getIsViewed(): bool
    {
        return auth('sanctum')->user() ? $this->myViews->isNotEmpty() : false;
    }

    public function myFavorites(): HasMany
    {
        return $this->hasMany(StoryFavorite::class)
            ->where('user_id', auth('sanctum')->user()->id);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(StoryFavorite::class);
    }

    public function getIsFavorite(): bool
    {
        return auth('sanctum')->user() ? $this->myFavorites->isNotEmpty() : false;
    }
}
