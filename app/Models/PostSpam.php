<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PostSpam
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int $spam_type_id
 * @property string|null $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read SpamType $spamType
 * @property-read User $user
 *
 * @method static Builder|PostSpam newModelQuery()
 * @method static Builder|PostSpam newQuery()
 * @method static Builder|PostSpam query()
 * @method static Builder|PostSpam whereCreatedAt($value)
 * @method static Builder|PostSpam whereId($value)
 * @method static Builder|PostSpam whereMessage($value)
 * @method static Builder|PostSpam wherePostId($value)
 * @method static Builder|PostSpam whereSpamTypeId($value)
 * @method static Builder|PostSpam whereUpdatedAt($value)
 * @method static Builder|PostSpam whereUserId($value)
 *
 * @mixin Eloquent
 */
class PostSpam extends Model
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
        'spam_type_id',
        'message',
    ];

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

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function spamType(): BelongsTo
    {
        return $this->belongsTo(SpamType::class);
    }
}
