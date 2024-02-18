<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $title
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, UserProfile> $userProfiles
 * @property-read int|null $user_profiles_count
 *
 * @method static Builder|Location newModelQuery()
 * @method static Builder|Location newQuery()
 * @method static Builder|Location query()
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereIsActive($value)
 * @method static Builder|Location whereTitle($value)
 * @method static Builder|Location whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'bool',
    ];

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }
}
