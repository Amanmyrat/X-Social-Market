<?php

namespace App\Models;

use App\Enum\NotificationType;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $recipient_id
 * @property int|null $initiator_id
 * @property int|null $post_id
 * @property int|null $story_id
 * @property NotificationType $type
 * @property string $reason
 * @property boolean $is_read
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post|null $post
 * @property-read Story|null $story
 * @property-read User $recipient
 * @property-read User $initiator
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification query()
 * @method static Builder|Notification whereBookingId($value)
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereInitiatorId($value)
 * @method static Builder|Notification whereIsRead($value)
 * @method static Builder|Notification whereRecipientId($value)
 * @method static Builder|Notification whereType($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'recipient_id',
        'initiator_id',
        'post_id',
        'story_id',
        'type',
        'reason',
        'is_read',
    ];

    protected $casts = [
        'type' => NotificationType::class
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
