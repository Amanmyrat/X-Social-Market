<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\FollowRequest
 *
 * @property int $id
 * @property int $followed_user_id
 * @property int $following_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Follower newModelQuery()
 * @method static Builder|Follower newQuery()
 * @method static Builder|Follower query()
 * @method static Builder|Follower whereCreatedAt($value)
 * @method static Builder|Follower whereFollowedUserId($value)
 * @method static Builder|Follower whereId($value)
 * @method static Builder|Follower whereUpdatedAt($value)
 * @method static Builder|Follower whereUserId($value)
 *
 * @mixin Eloquent
 */
class FollowRequest extends Model
{
    use HasFactory;
}
