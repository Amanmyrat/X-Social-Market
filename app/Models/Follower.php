<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Follower
 *
 * @property int $id
 * @property int $user_id
 * @property int $follow_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $unfollowed_at
 *
 * @method static Builder|Follower newModelQuery()
 * @method static Builder|Follower newQuery()
 * @method static Builder|Follower query()
 * @method static Builder|Follower whereCreatedAt($value)
 * @method static Builder|Follower whereFollowedUserId($value)
 * @method static Builder|Follower whereFollowingUserId($value)
 * @method static Builder|Follower whereId($value)
 * @method static Builder|Follower whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Follower extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'follow_user_id',
        'unfollowed_at',
    ];

    /**
     * User who follows.
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * User being followed.
     */
    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_user_id');
    }
}
