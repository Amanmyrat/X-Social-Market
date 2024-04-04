<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $chat_id
 * @property int $sender_user_id
 * @property int $receiver_user_id
 * @property string $type
 * @property string|null $body
 * @property array|null $extra
 * @property string|null $read_at
 * @property string|null $sender_deleted_at
 * @property string|null $receiver_deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Chat $chat
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read ?array $image_urls
 *
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message query()
 * @method static Builder|Message whereBody($value)
 * @method static Builder|Message whereChatId($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereExtra($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereReadAt($value)
 * @method static Builder|Message whereReceiverDeletedAt($value)
 * @method static Builder|Message whereReceiverUserId($value)
 * @method static Builder|Message whereSenderDeletedAt($value)
 * @method static Builder|Message whereSenderUserId($value)
 * @method static Builder|Message whereType($value)
 * @method static Builder|Message whereUpdatedAt($value)
 *
 * @mixin Eloquent
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('message_medias')
            ->useDisk('messages');
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('large')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(1024)
            ->optimize()
            ->performOnCollections('message_medias');

        $this->addMediaConversion('medium')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(768)
            ->optimize()
            ->performOnCollections('message_medias');

        $this->addMediaConversion('thumb')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(100)
            ->blur(1)
            ->optimize()
            ->performOnCollections('message_medias');
    }

    public function getImageUrlsAttribute(): ?array
    {
        if (! $this->hasMedia('message_medias')) {
            return null;
        }

        $medias = [];
        foreach ($this->getMedia('message_medias') as $media) {

            $mediaUrls = ['original_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3))];

            if ($this->type == 'image') {
                $mediaUrls += [
                    'large_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'large'),
                    'medium_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'medium'),
                    'thumb_url' => $media->getTemporaryUrl(Carbon::now()->addDays(3), 'thumb'),
                ];
            }

            array_push($medias, $mediaUrls);
        }

        return $medias;
    }

    public function getExtraAttribute(): ?array
    {
        if ($this->type == Message::TYPE_MEDIA) {
            return [
                'medias' => $this->image_urls,
            ];
        }

        return $this->extra;
    }
}
