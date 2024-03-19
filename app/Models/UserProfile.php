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
 * App\Models\UserProfile
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $full_name
 * @property string|null $bio
 * @property string|null $website
 * @property Carbon|null $birthdate
 * @property string|null $gender
 * @property bool $payment_available
 * @property bool $verified
 * @property bool $private
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $category_id
 * @property int|null $location_id
 * @property MediaCollection<int, Media> $media
 * @property-read Category|null $category
 * @property-read Location|null $location
 * @property-read User $user
 * @property-read ?array $image_urls
 *
 * @method static Builder|UserProfile newModelQuery()
 * @method static Builder|UserProfile newQuery()
 * @method static Builder|UserProfile query()
 * @method static Builder|UserProfile whereBio($value)
 * @method static Builder|UserProfile whereBirthdate($value)
 * @method static Builder|UserProfile whereCategoryId($value)
 * @method static Builder|UserProfile whereCreatedAt($value)
 * @method static Builder|UserProfile whereFullName($value)
 * @method static Builder|UserProfile whereGender($value)
 * @method static Builder|UserProfile whereId($value)
 * @method static Builder|UserProfile whereLocationId($value)
 * @method static Builder|UserProfile wherePaymentAvailable($value)
 * @method static Builder|UserProfile wherePrivate($value)
 * @method static Builder|UserProfile whereProfileImage($value)
 * @method static Builder|UserProfile whereUpdatedAt($value)
 * @method static Builder|UserProfile whereUserId($value)
 * @method static Builder|UserProfile whereVerified($value)
 * @method static Builder|UserProfile whereWebsite($value)
 *
 * @mixin Eloquent
 */
class UserProfile extends Model implements HasMedia
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
        'full_name',
        'bio',
        'location_id',
        'category_id',
        'website',
        'birthdate',
        'gender',
        'payment_available',
        'verified',
        'private',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'datetime',
    ];

    /**
     * Get the user that owns the phone.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): ?BelongsTo
    {
        return $this->belongsTo(Location::class)->withDefault();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('user_images')
            ->useDisk('users')
            ->singleFile();
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('large')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(1024)
            ->optimize()
            ->performOnCollections('user_images');

        $this->addMediaConversion('medium')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(768)
            ->optimize()
            ->performOnCollections('user_images');

        $this->addMediaConversion('thumb')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(100)
            ->blur(1)
            ->optimize()
            ->performOnCollections('user_images');
    }

    public function getImageUrlsAttribute(): ?array
    {
        if (!$this->hasMedia('user_images')) {
            return null;
        }

        return [
            'original_url' => $this->getFirstMedia('user_images')->getTemporaryUrl(Carbon::now()->addDays(3)),
            'large_url' => $this->getFirstMedia('user_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'large'),
            'medium_url' => $this->getFirstMedia('user_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'medium'),
            'thumb_url' => $this->getFirstMedia('user_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'thumb'),
        ];
    }
}
