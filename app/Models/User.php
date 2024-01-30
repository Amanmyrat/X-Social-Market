<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @mixin Eloquent
 * @property int id
 * @property string phone
 * @property string username
 * @property string email
 * @property string password
 * @property string type
 * @property string device_token
 * @property string last_activity
 * @property bool is_online
 * @property bool is_active
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
        return $this->belongsToMany(User::class, 'followers', 'following_user_id', 'user_id')->withTimestamps();
    }

    /**
     * Get user followings
     */
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'following_user_id')->withTimestamps();
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
     * Get user blocked list
     */
    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id')->withTimestamps();
    }

    public function chats()
    {
        return Chat::where(function ($query) {
            $query->where('sender_user_id', $this->id)
                ->orWhere('receiver_user_id', $this->id);
        })->whereNotDeleted();
    }


}
