<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PostComment
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int $rating
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read User $user
 *
 * @method static Builder|PostRating newModelQuery()
 * @method static Builder|PostRating newQuery()
 * @method static Builder|PostRating query()
 * @method static Builder|PostRating whereCreatedAt($value)
 * @method static Builder|PostRating whereId($value)
 * @method static Builder|PostRating wherePostId($value)
 * @method static Builder|PostRating whereRating($value)
 * @method static Builder|PostRating whereUpdatedAt($value)
 * @method static Builder|PostRating whereUserId($value)
 *
 * @mixin Eloquent
 */
class PostRating extends Model
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
        'rating',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
