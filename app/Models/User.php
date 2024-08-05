<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $phone
 * @property string $username
 * @property string|null $email
 * @property Carbon|null $email_verified_at
 * @property mixed $password
 * @property string $type
 * @property string|null $device_token
 * @property Carbon|null $last_activity
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_online
 * @property bool $is_active
 * @property Carbon|null $blocked_at
 * @property string|null $block_reason
 * @property-read Collection<int, User> $blockedUsers
 * @property-read int|null $blocked_users_count
 * @property-read Collection<int, PostBookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read Collection<int, PostFavorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read Collection<int, User> $followers
 * @property-read int|null $followers_count
 * @property-read Collection<int, User> $followings
 * @property-read int|null $followings_count
 * @property-read Collection<int, User> $incomingRequests
 * @property-read int|null $incoming_requests_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, User> $outgoingRequests
 * @property-read int|null $outgoing_requests_count
 * @property-read Collection<int, PostView> $postViews
 * @property-read int|null $post_views_count
 * @property-read Collection<int, Post> $posts
 * @property-read int|null $posts_count
 * @property-read UserProfile|null $profile
 * @property-read Collection<int, PostRating> $ratings
 * @property-read int|null $ratings_count
 * @property-read Collection<int, Story> $stories
 * @property-read int|null $stories_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read float $ratings_avg_rating
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereBlockReason($value)
 * @method static Builder|User whereBlockedAt($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeviceToken($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsActive($value)
 * @method static Builder|User whereIsOnline($value)
 * @method static Builder|User whereLastActivity($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereType($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 *
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const TYPE_USER = 'user';

    public const TYPE_SELLER = 'seller';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone',
        'username',
        'email',
        'password',
        'type',
        'device_token',
        'last_activity',
        'is_online',
        'is_active',
        'blocked_at',
        'block_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'device_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
        'blocked_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'bool',
        'is_online' => 'bool',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    /**
     * Get all the followers for the user.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follow_user_id', 'user_id')
            ->withPivot('unfollowed_at')
            ->wherePivotNull('unfollowed_at')
            ->withTimestamps();
    }

    /**
     * Get all the users this user is following.
     */
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follow_user_id')
            ->withPivot('unfollowed_at')
            ->wherePivotNull('unfollowed_at')
            ->withTimestamps();
    }

    /**
     * Get user outgoing follow requests
     */
    public function outgoingRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follow_requests', 'following_user_id', 'followed_user_id')->withTimestamps();
    }

    /**
     * Get user incoming follow requests
     */
    public function incomingRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follow_requests', 'followed_user_id', 'following_user_id')->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(PostFavorite::class)->orderByDesc('created_at');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(PostBookmark::class)->orderByDesc('created_at');
    }

    public function postViews(): HasMany
    {
        return $this->hasMany(PostView::class)->orderByDesc('created_at');
    }

    public function storyViews(): HasMany
    {
        return $this->hasMany(StoryView::class);
    }

    public function storyFavorites(): HasMany
    {
        return $this->hasMany(StoryFavorite::class);
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id')->withTimestamps();
    }

    public function chats(): Builder|Chat
    {
        return Chat::where(function ($query) {
            $query->where('sender_user_id', $this->id)
                ->orWhere('receiver_user_id', $this->id);
        })->whereNotDeleted();
    }

    public function ratings(): HasManyThrough
    {
        return $this->hasManyThrough(PostRating::class, Post::class);
    }

    // Reports this user has made
    public function reportsMade(): HasMany
    {
        return $this->hasMany(UserReport::class, 'user_id');
    }

    // Reports where this user is reported by others
    public function reportsAgainst(): HasMany
    {
        return $this->hasMany(UserReport::class, 'reported_user_id');
    }

    public function latestReportAgainst(): HasOne
    {
        return $this->hasOne(UserReport::class, 'reported_user_id')->latestOfMany();
    }
}
