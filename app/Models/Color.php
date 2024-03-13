<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Color
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Color newModelQuery()
 * @method static Builder|Color newQuery()
 * @method static Builder|Color query()
 * @method static Builder|Color whereCode($value)
 * @method static Builder|Color whereCreatedAt($value)
 * @method static Builder|Color whereId($value)
 * @method static Builder|Color whereIsActive($value)
 * @method static Builder|Color whereTitle($value)
 * @method static Builder|Color whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Color extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'code',
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

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_color');
    }
}
