<?php

namespace App\Models;

use App\Contracts\NotifiableModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Story
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon $valid_until
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $post_id
 * @property MediaCollection<int, Media> $media
 * @property ?mixed $image
 * @property bool $is_active
 * @property Carbon|null $blocked_at
 * @property string|null $block_reason
 * @property-read Collection<int, StoryFavorite> $favorites
 * @property-read ?array $image_urls
 * @property-read int|null $favorites_count
 * @property-read Post|null $post
 * @property-read User $user
 * @property-read Collection<int, StoryView> $views
 * @property-read int|null $views_count
 *
 * @method static Builder|Story newModelQuery()
 * @method static Builder|Story newQuery()
 * @method static Builder|Story query()
 * @method static Builder|Story whereCreatedAt($value)
 * @method static Builder|Story whereId($value)
 * @method static Builder|Story whereImage($value)
 * @method static Builder|Story wherePostId($value)
 * @method static Builder|Story whereUpdatedAt($value)
 * @method static Builder|Story whereUserId($value)
 * @method static Builder|Story whereValidUntil($value)
 *
 * @mixin Eloquent
 */
class Story extends BaseModel implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'valid_until',
        'is_active',
        'blocked_at',
        'block_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'valid_until',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class)->with('media');
    }

    public function views(): HasMany
    {
        return $this->hasMany(StoryView::class)->with('user');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(StoryFavorite::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('story_images')
            ->useDisk('stories')
            ->singleFile();
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
            ->performOnCollections('story_images');

        $this->addMediaConversion('medium')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(768)
            ->optimize()
            ->performOnCollections('story_images');

        $this->addMediaConversion('thumb')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(100)
            ->blur(1)
            ->optimize()
            ->performOnCollections('story_images');
    }

    public function getImageUrlsAttribute(): ?array
    {
        if (! $this->hasMedia('story_images')) {
            return null;
        }

        return [
            'original_url' => $this->getFirstMedia('story_images')->getTemporaryUrl(Carbon::now()->addDays(3)),
            'large_url' => $this->getFirstMedia('story_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'large'),
            'medium_url' => $this->getFirstMedia('story_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'medium'),
            'thumb_url' => $this->getFirstMedia('story_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'thumb'),
        ];
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
