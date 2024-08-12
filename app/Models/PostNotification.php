<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PostNotification
 *
 * @property int $id
 * @property int $post_id
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property int $comment_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $notifiable
 * @property-read Post $post
 * @property-read PostComment $comment
 *
 * @method static Builder|PostNotification newModelQuery()
 * @method static Builder|PostNotification newQuery()
 * @method static Builder|PostNotification query()
 * @method static Builder|PostNotification whereCreatedAt($value)
 * @method static Builder|PostNotification whereId($value)
 * @method static Builder|PostNotification whereNotifiableId($value)
 * @method static Builder|PostNotification whereNotifiableType($value)
 * @method static Builder|PostNotification wherePostId($value)
 * @method static Builder|PostNotification whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class PostNotification extends BaseModel
{
    use HasFactory;

    protected $fillable = ['post_id', 'is_read', 'comment_id', 'reason'];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(PostComment::class);
    }
}
