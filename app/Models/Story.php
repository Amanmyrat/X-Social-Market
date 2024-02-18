<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Story
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $post_id
 * @property string|null $image
 * @property Carbon $valid_until
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, StoryFavorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read Post|null $post
 * @property-read User $user
 * @property-read Collection<int, StoryView> $views
 * @property-read int|null $views_count
 *
 * @method static Builder|Story newModelQuery()
 * @method static Builder|Story newQuery()
 * @method static Builder|Story query()
 * @method static Builder|Story whereCreatedAt($value)
 * @method static Builder|Story whereId($value)
 * @method static Builder|Story whereImage($value)
 * @method static Builder|Story wherePostId($value)
 * @method static Builder|Story whereUpdatedAt($value)
 * @method static Builder|Story whereUserId($value)
 * @method static Builder|Story whereValidUntil($value)
 *
 * @mixin Eloquent
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
        /** @var User $user */
        $user = Auth::user();

        return $this->hasMany(StoryView::class)
            ->where('user_id', $user->id);
    }

    public function getIsViewed(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user ? $this->myViews->isNotEmpty() : false;
    }

    public function myFavorites(): HasMany
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->hasMany(StoryFavorite::class)
            ->where('user_id', $user->id);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(StoryFavorite::class);
    }

    public function getIsFavorite(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user ? $this->myFavorites->isNotEmpty() : false;
    }
}
