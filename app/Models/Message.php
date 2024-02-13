<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Message
 *
 * @mixin Eloquent
 *
 * @property int id
 * @property int chat_id
 * @property int sender_user_id
 * @property int receiver_user_id
 * @property string type
 * @property string body
 * @property array extra
 * @property string read_at
 * @property string sender_deleted_at
 * @property string receiver_deleted_at
 * @property string created_at
 */
class Message extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const TYPE_MESSAGE = 'message';

    public const TYPE_SHARE_STORY = 'share_story';

    public const TYPE_SHARE_POST = 'share_post';

    public const TYPE_MEDIA = 'media';

    public const TYPE_FILE = 'file';

    protected $fillable = [
        'chat_id',
        'sender_user_id',
        'receiver_user_id',
        'type',
        'body',
        'extra',
        'read_at',
        'sender_deleted_at',
        'receiver_deleted_at',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    protected array $dates = ['read_at', 'sender_deleted_at', 'receiver_deleted_at'];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
