<?php

namespace App\Models;

use App\Contracts\NotifiableModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\PostFavorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, PostNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Post $post
 * @property-read User $user
 *
 * @method static Builder|PostFavorite newModelQuery()
 * @method static Builder|PostFavorite newQuery()
 * @method static Builder|PostFavorite query()
 * @method static Builder|PostFavorite whereCreatedAt($value)
 * @method static Builder|PostFavorite whereId($value)
 * @method static Builder|PostFavorite wherePostId($value)
 * @method static Builder|PostFavorite whereUpdatedAt($value)
 * @method static Builder|PostFavorite whereUserId($value)
 *
 * @mixin Eloquent
 */
class PostFavorite extends Model implements NotifiableModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'post_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($favorite) {
            // Delete associated notifications
            $favorite->notifications()->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(PostNotification::class, 'notifiable');
    }
}
