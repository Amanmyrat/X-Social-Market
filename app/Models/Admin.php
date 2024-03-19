<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property bool $is_active
 * @property Carbon|null $last_activity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property MediaCollection<int, Media> $media
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read ?array $image_urls
 *
 * @method static Builder|Admin newModelQuery()
 * @method static Builder|Admin newQuery()
 * @method static Builder|Admin query()
 * @method static Builder|Admin whereCreatedAt($value)
 * @method static Builder|Admin whereEmail($value)
 * @method static Builder|Admin whereId($value)
 * @method static Builder|Admin whereIsSuper($value)
 * @method static Builder|Admin whereName($value)
 * @method static Builder|Admin wherePassword($value)
 * @method static Builder|Admin whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Admin extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, HasRoles;
    use InteractsWithMedia;

    protected string $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'phone',
        'email',
        'password',
        'is_active',
        'last_activity',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('admin_images')
            ->useDisk('admins')
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
            ->performOnCollections('admin_images');

        $this->addMediaConversion('medium')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(768)
            ->optimize()
            ->performOnCollections('admin_images');

        $this->addMediaConversion('thumb')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(100)
            ->blur(1)
            ->optimize()
            ->performOnCollections('admin_images');
    }

    public function getImageUrlsAttribute(): ?array
    {
        if (!$this->hasMedia('admin_images')) {
            return null;
        }

        return [
            'original_url' => $this->getFirstMedia('admin_images')->getTemporaryUrl(Carbon::now()->addDays(3)),
            'large_url' => $this->getFirstMedia('admin_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'large'),
            'medium_url' => $this->getFirstMedia('admin_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'medium'),
            'thumb_url' => $this->getFirstMedia('admin_images')->getTemporaryUrl(Carbon::now()->addDays(3), 'thumb'),
        ];
    }
}
