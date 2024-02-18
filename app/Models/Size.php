<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Size
 *
 * @property int $id
 * @property string $title
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Size newModelQuery()
 * @method static Builder|Size newQuery()
 * @method static Builder|Size query()
 * @method static Builder|Size whereCreatedAt($value)
 * @method static Builder|Size whereId($value)
 * @method static Builder|Size whereIsActive($value)
 * @method static Builder|Size whereTitle($value)
 * @method static Builder|Size whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Size extends Model
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
}
