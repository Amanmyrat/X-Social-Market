<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\PostComment
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int $user_id
 * @property int $post_id
 * @property string $comment
 * @property bool $is_active
 * @property Carbon|null $blocked_at
 * @property string|null $block_reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, PostComment> $children
 * @property-read int|null $children_count
 * @property-read int|null $notifications_count
 * @property-read PostComment|null $parent
 * @property-read Post $post
 * @property-read User $user
 *
 * @method static Builder|PostComment newModelQuery()
 * @method static Builder|PostComment newQuery()
 * @method static Builder|PostComment query()
 * @method static Builder|PostComment whereComment($value)
 * @method static Builder|PostComment whereCreatedAt($value)
 * @method static Builder|PostComment whereId($value)
 * @method static Builder|PostComment whereParentId($value)
 * @method static Builder|PostComment wherePostId($value)
 * @method static Builder|PostComment whereUpdatedAt($value)
 * @method static Builder|PostComment whereUserId($value)
 *
 * @mixin Eloquent
 */
class PostComment extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'user_id',
        'post_id',
        'comment',
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
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($comment) {
            // Delete associated notifications
            $comment->notifications()->delete();
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }
}
