<?php

namespace App\Models;

use Auth;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $media_type
 * @property string $caption
 * @property int $price
 * @property string $description
 * @property string $location
 * @property bool $can_comment
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property MediaCollection<int, Media> $media
 * @property-read Collection<int, PostBookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read Category $category
 * @property-read Collection<int, Chat> $chats
 * @property-read int|null $chats_count
 * @property-read Collection<int, PostComment> $comments
 * @property-read int|null $comments_count
 * @property-read Collection<int, User> $favoriteByUsers
 * @property-read int|null $favorite_by_users_count
 * @property-read Collection<int, PostFavorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read int|null $media_count
 * @property-read Product|null $product
 * @property-read Collection<int, PostRating> $ratings
 * @property-read int|null $ratings_count
 * @property-read User $user
 * @property-read Collection<int, PostView> $views
 * @property-read int|null $views_count
 * @property-read float $ratings_avg_rating
 * @property-read bool $is_following
 *
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereCanComment($value)
 * @method static Builder|Post whereCaption($value)
 * @method static Builder|Post whereCategoryId($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereDescription($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereLocation($value)
 * @method static Builder|Post whereMediaType($value)
 * @method static Builder|Post wherePrice($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post whereUserId($value)
 * @method static Builder|Post withIsFollowing()
 *
 * @mixin Eloquent
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
        'is_active',
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
     * @var array<string, string>
     */
    protected $casts = [
        'can_comment' => 'boolean',
        'is_active' => 'boolean',
        'ratings_avg_rating' => 'float',
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

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    public function postReports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }

    public function latestReport(): HasOne
    {
        return $this->hasOne(PostReport::class)->latestOfMany();
    }

    /**
     * Scope a query to add 'is_following' attribute indicating if the post's creator is followed by the given user.
     */
    public function scopeWithIsFollowing($query)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return $query;
        }

        return $query->leftJoin('followers', function ($join) use ($user) {
            $join->on('followers.following_user_id', '=', 'posts.user_id')
                ->where('followers.followed_user_id', '=', $user->id);
        })->addSelect(['posts.*', DB::raw('CASE WHEN followers.followed_user_id IS NOT NULL THEN true ELSE false END')]);
    }

    public function scopeIsActive($query){
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to enhance it with a complex scoring system for post recommendations,
     * including user engagement and preferences. It calculates a dynamic score for each post
     * based on several criteria:
     * - Engagement (likes, comments, bookmarks) with different weights (Trendy or engaging posts)
     * - Recency of the post
     * - Whether the post is from a followed user
     * - Whether the post belongs to a category that the user has shown interest in (Posts likely to be of interest (based on category))
     * - The novelty of the post (favoring posts not yet viewed by the user)(Freshness of content (non-viewed posts))
     * This scope also integrates additional functionality:
     * - Eager loads related user profiles and media
     * - Calculates the average rating of each post
     * - Filters by active posts
     * - Adds a custom select to indicate if the current user is following the post's author
     *
     * @param Builder $query
     * @param int $userId User ID for whom the recommendations are tailored
     * @return Builder
     */
    public function scopeWithRecommendationScore(Builder $query, int $userId): Builder
    {
        $scoreSelect = "
        posts.id,
        (
            SELECT COUNT(*) FROM post_favorites WHERE post_favorites.post_id = posts.id
        ) * 1 +
        (
            SELECT COUNT(*) FROM post_comments WHERE post_comments.post_id = posts.id
        ) * 2 +
        (
            SELECT COUNT(*) FROM post_bookmarks WHERE post_bookmarks.post_id = posts.id
        ) * 1 +
        (
            CASE WHEN EXISTS (
                SELECT 1 FROM followers WHERE followers.followed_user_id = posts.user_id AND followers.following_user_id = $userId
            ) THEN 100 ELSE 0 END
        ) +
        (
            CASE WHEN NOT EXISTS (
                SELECT 1 FROM post_views WHERE post_views.post_id = posts.id AND post_views.user_id = $userId
            ) THEN 50 ELSE 0 END
        ) +
        (
           CASE WHEN posts.category_id IN (
                SELECT posts.category_id FROM posts
                JOIN post_favorites ON post_favorites.post_id = posts.id AND post_favorites.user_id = $userId
                UNION
                SELECT posts.category_id FROM posts
                JOIN post_comments ON post_comments.post_id = posts.id AND post_comments.user_id = $userId
                UNION
                SELECT posts.category_id FROM posts
                JOIN post_bookmarks ON post_bookmarks.post_id = posts.id AND post_bookmarks.user_id = $userId
            ) THEN 10 ELSE 0 END
        ) +
        GREATEST(5 - EXTRACT(DAY FROM NOW() - posts.created_at), 0) AS score
    ";

        $subQuery = DB::table('posts')
            ->selectRaw($scoreSelect)
            ->where('posts.is_active', true)
            ->groupBy('posts.id');

        $isFollowingSelect = DB::raw('CASE WHEN followers.followed_user_id IS NOT NULL THEN true ELSE false END AS is_following');

        return $query->joinSub($subQuery, 'scored_posts', function ($join) {
            $join->on('posts.id', '=', 'scored_posts.id');
        })
            ->leftJoin('followers', function ($join) use ($userId) {
                $join->on('followers.following_user_id', '=', 'posts.user_id')
                    ->where('followers.followed_user_id', '=', $userId);
            })
            ->select('posts.*', 'scored_posts.score', $isFollowingSelect)
            ->with(['user.profile', 'media'])
            ->withAvg('ratings', 'rating')
            ->orderBy('scored_posts.score', 'DESC');
    }
}
