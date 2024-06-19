<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BlockedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $blocked_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $blockedUser
 * @property-read User $user
 *
 * @method static Builder|BlockedUser newModelQuery()
 * @method static Builder|BlockedUser newQuery()
 * @method static Builder|BlockedUser query()
 * @method static Builder|BlockedUser whereBlockedUserId($value)
 * @method static Builder|BlockedUser whereCreatedAt($value)
 * @method static Builder|BlockedUser whereId($value)
 * @method static Builder|BlockedUser whereUpdatedAt($value)
 * @method static Builder|BlockedUser whereUserId($value)
 *
 * @mixin Eloquent
 */
class BlockedUser extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'blocked_user_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blockedUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
