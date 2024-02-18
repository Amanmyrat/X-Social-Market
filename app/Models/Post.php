<?php

namespace App\Models;

use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Post
 *
 * @mixin Eloquent
 *
 * @property int id
 * @property int user_id
 * @property int category_id
 * @property string caption
 * @property int price
 * @property string description
 * @property string media_type
 * @property bool can_comment
 * @property string created_at
 * @property string location
 */
class Post extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'media_type',
        'caption',
        'price',
        'description',
        'location',
        'can_comment',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $casts = [
        'can_comment' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function favoriteByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_favorites', 'post_id', 'user_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(PostFavorite::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(PostBookmark::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->where('parent_id', 0)->orderByDesc('created_at');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(PostRating::class)->orderByDesc('created_at');
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class)->with('user');
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    /**
     * Scope a query to add 'is_following' attribute indicating if the post's creator is followed by the given user.
     */
    public function scopeWithIsFollowing($query)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $query;
        }

        return $query->leftJoin('followers', function ($join) use ($user) {
            $join->on('followers.followed_user_id', '=', 'posts.user_id')
                ->where('followers.user_id', '=', $user->id);
        })->addSelect(['posts.*', DB::raw('IF(followers.user_id IS NOT NULL, true, false) AS is_following')]);
    }

}
