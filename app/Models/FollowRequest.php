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
 * @property int $following_user_id
 * @property int $followed_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|FollowRequest newModelQuery()
 * @method static Builder|FollowRequest newQuery()
 * @method static Builder|FollowRequest query()
 * @method static Builder|FollowRequest whereCreatedAt($value)
 * @method static Builder|FollowRequest whereFollowedUserId($value)
 * @method static Builder|FollowRequest whereFollowingUserId($value)
 * @method static Builder|FollowRequest whereId($value)
 * @method static Builder|FollowRequest whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class FollowRequest extends BaseModel
{
    use HasFactory;
}
