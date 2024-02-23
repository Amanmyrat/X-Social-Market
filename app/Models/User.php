<?php

namespace App\Models;

use Carbon\Carbon;
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
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $phone
 * @property string $username
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string $type
 * @property string|null $device_token
 * @property \Illuminate\Support\Carbon|null $last_activity
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_online
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $blocked_at
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
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, PostView> $postViews
 * @property-read int|null $post_views_count
 * @property-read Collection<int, Post> $posts
 * @property-read int|null $posts_count
 * @property-read UserProfile|null $profile
 * @property-read Collection<int, Story> $stories
 * @property-read int|null $stories_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read int ratings_avg_rating
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
        'block_reason ',
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

    /**
     * Get the user profile record associated with the user.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get stories record associated with the user.
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class)->where('valid_until', '>', Carbon::now());
    }

    /**
     * Get user followers.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_user_id', 'user_id')->withTimestamps();
    }

    /**
     * Get user followings
     */
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'followed_user_id')->withTimestamps();
    }

    /**
     * Get posts record associated with the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get favorites associated with the user.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(PostFavorite::class)->orderByDesc('created_at');
    }

    /**
     * Get bookmarks associated with the user.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(PostBookmark::class)->orderByDesc('created_at');
    }

    /**
     * Get bookmarks associated with the user.
     */
    public function postViews(): HasMany
    {
        return $this->hasMany(PostView::class)->orderByDesc('created_at');
    }

    /**
     * Get user blocked list
     */
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
}
