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
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
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
 * @property-read bool $has_unviewed_story
 * @property-read ?array $image_urls
 * @property-read ?array $first_image_urls
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
 * @method static Builder|Post withRecommendationScore(int $userId)
 * @method static Builder|Post activeAndNotBlocked(?int $userId)
 * @method static Builder|Post activeNotBlockedAndWithFollowingStatus(?int $userId)
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

    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to enhance it with a complex scoring system for post recommendations.
     * This includes user engagement metrics and preferences to calculate a dynamic score
     * for each post based on several criteria:
     *
     * - Engagement: Factors in favorites, comments, and bookmarks, with different weights.
     *   This identifies trendy or engaging posts.
     * - Recency: Prioritizes newer posts.
     * - Following Status: Boosts posts from users the current user is following.
     * - Category Interest: Increases score for posts in categories the user has shown interest in,
     *   based on their interactions (favorites, comments, bookmarks).
     * - Novelty: Favors posts not yet viewed by the user to ensure fresh content.
     * - Unviewed Stories: Adds a flag to indicate if there's an unviewed story from the post's author,
     *   considering only stories created within the last 24 hours.
     *
     * Additionally, the scope:
     * - Filters out posts from users who are blocked by the current user or by admins.
     * - Excludes posts from users with a private profile, unless the user is followed by the current user.
     * - Eager loads related user profiles and media for efficient data retrieval.
     * - Calculates and includes the average rating of each post.
     * - Orders the posts by their calculated score in descending order.
     *
     * @param Builder $query The initial query builder instance.
     * @param int $userId The ID of the user for whom recommendations are being tailored.
     * @return Builder The modified query builder instance with applied conditions for recommendations.
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
            GREATEST(5 - EXTRACT(DAY FROM NOW() - posts.created_at), 0) AS score,
            (
                SELECT CASE WHEN COUNT(stories.id) > 0 THEN true ELSE false END
                FROM stories
                LEFT JOIN story_views ON stories.id = story_views.story_id AND story_views.user_id = $userId
                WHERE stories.user_id = posts.user_id
                AND stories.created_at >= NOW() - INTERVAL '24 hours'
                AND story_views.id IS NULL
            ) AS has_unviewed_story
        ";

        $subQuery = DB::table('posts')
            ->selectRaw($scoreSelect)
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->leftJoin('followers', function ($join) use ($userId) {
                $join->on('followers.followed_user_id', '=', 'users.id')
                    ->where('followers.following_user_id', '=', $userId);
            })
            ->leftJoin('blocked_users', function ($join) use ($userId) {
                $join->on('users.id', '=', 'blocked_users.blocked_user_id')
                    ->where('blocked_users.user_id', '=', $userId); // Current user is blocking
            })
            ->where('posts.is_active', true)
            ->whereNull('users.blocked_at') // Admin has not blocked the user
            ->where('users.is_active', true) // Admin has not disabled user
            ->whereNull('blocked_users.id') // Current user has not blocked the user
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) {
                    // Include posts if the profile is not private or does not exist
                    $q->whereNull('user_profiles.private') // Profile is either not private
                    ->orWhere('user_profiles.private', '=', false);
                })->orWhereExists(function ($q) use ($userId) {
                    // Or the current user is following the post's user
                    $q->select(DB::raw(1))
                        ->from('followers')
                        ->whereRaw('followers.followed_user_id = users.id')
                        ->where('followers.following_user_id', '=', $userId);
                });
            })
            ->groupBy('posts.id');


        return $query->joinSub($subQuery, 'scored_posts', function ($join) {
            $join->on('posts.id', '=', 'scored_posts.id');
        })
            ->leftJoin('followers as f2', function ($join) use ($userId) {
                // Ensure the is_following flag is correctly set for each post
                $join->on('f2.following_user_id', '=', 'posts.user_id')
                    ->where('f2.followed_user_id', '=', $userId);
            })
            ->select('posts.*', 'scored_posts.score', 'scored_posts.has_unviewed_story', DB::raw('CASE WHEN f2.followed_user_id IS NOT NULL THEN true ELSE false END AS is_following'))
            ->with(['user.profile', 'media'])
            ->withAvg('ratings', 'rating')
            ->orderBy('scored_posts.score', 'DESC');
    }

    /**
     * Scope a query to only include active posts from users who are not blocked by the current user, have not been blocked by admins,
     * and do not have a private profile unless the current user is following them. This scope ensures that the posts returned
     * are suitable for public viewing, adhering to user privacy settings, admin actions, and respecting user-follow relationships.
     *
     * - Filters posts to only include those that are marked as active.
     * - Excludes posts from users who have been blocked by the current user, leveraging the 'blocked_users' table.
     * - Excludes posts from users who have been blocked by admins, indicated by a non-null 'blocked_at' field in the 'users' table.
     * - Excludes posts from users with a private profile, unless those users are followed by the current user.
     *
     * @param Builder $query The initial query builder instance.
     * @param int|null $userId The ID of the current user, to filter out posts from users blocked by them and handle privacy checks. Null if the user is not logged in.
     * @return Builder The modified query builder instance with applied filters.
     */
    public function scopeActiveAndNotBlocked(Builder $query, ?int $userId): Builder
    {
        return $query->select('posts.*')->where('posts.is_active', true)
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->leftJoin('followers', function ($join) use ($userId) {
                $join->on('followers.followed_user_id', '=', 'users.id')
                    ->where('followers.following_user_id', '=', $userId);
            })
            ->leftJoin('blocked_users', function ($join) use ($userId) {
                $join->on('users.id', '=', 'blocked_users.blocked_user_id')
                    ->where('blocked_users.user_id', '=', $userId);
            })
            ->whereNull('users.blocked_at') // Admin has not blocked the user
            ->where('users.is_active', true) // Admin has not disabled user
            ->whereNull('blocked_users.id') // Current user has not blocked the user
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) {
                    // Include posts if the profile is not private or does not exist
                    $q->whereNull('user_profiles.private')
                        ->orWhere('user_profiles.private', '=', false);
                })->orWhere(function ($q) use ($userId) {
                    // Or the current user is following the post's user
                    $q->whereNotNull('followers.followed_user_id');
                });
            });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('post_medias')
            ->useDisk('posts');
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('large')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(1024)
            ->optimize()
            ->performOnCollections('post_medias');

        $this->addMediaConversion('medium')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(768)
            ->optimize()
            ->performOnCollections('post_medias');

        $this->addMediaConversion('thumb')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(100)
            ->blur(1)
            ->optimize()
            ->performOnCollections('post_medias');
    }

    public function getFirstImageUrlsAttribute(): ?array
    {
        if (!$this->hasMedia('post_medias')) {
            return null;
        }

        $media = $this->getFirstMedia('post_medias');

        if ($this->media_type == 'video') {
            return [
                'original_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3)),
            ];
        }

        return [
            'original_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3)),
            'large_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'large'),
            'medium_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'medium'),
            'thumb_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'thumb'),
        ];
    }

    public function getImageUrlsAttribute(): ?array
    {
        if (!$this->hasMedia('post_medias')) {
            return null;
        }

        $medias = [];
        foreach ($this->getMedia('post_medias') as $media) {

            $mediaUrls = ['original_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3))];

            if ($this->media_type === 'image') {
                $mediaUrls += [
                    'large_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'large'),
                    'medium_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'medium'),
                    'thumb_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'thumb'),
                ];
            }

            array_push($medias, $mediaUrls);
        }

        return $medias;
    }
}
