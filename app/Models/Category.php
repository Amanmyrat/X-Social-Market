<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $icon
 * @property bool $is_active
 * @property bool $has_product
 * @property MediaCollection<int, Media> $media
 * @property-read Collection<int, Post> $posts
 * @property-read int|null $posts_count
 * @property-read Collection<int, UserProfile> $userProfiles
 * @property-read int|null $user_profiles_count
 * @property-read ?array $image_urls
 *
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDescription($value)
 * @method static Builder|Category whereHasProduct($value)
 * @method static Builder|Category whereIcon($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereIsActive($value)
 * @method static Builder|Category whereTitle($value)
 * @method static Builder|Category whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Category extends BaseModel implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'is_active',
        'has_product',
    ];

    public $translatable = ['title', 'description'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'bool',
        'has_product' => 'bool',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_images')
            ->useDisk('categories')
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
            ->performOnCollections('category_images');

        $this->addMediaConversion('thumb')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(100)
            ->blur(1)
            ->optimize()
            ->performOnCollections('category_images');
    }

    public function getImageUrlsAttribute(): ?array
    {
        if (! $this->hasMedia('category_images')) {
            return null;
        }

        return [
            'original_url' => $this->getFirstMedia('category_images')->getTemporaryUrl(Carbon::now()->addDays(3)),
            'large_url' => $this->getFirstMedia('category_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'large'),
            'thumb_url' => $this->getFirstMedia('category_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'thumb'),
        ];
    }
}
