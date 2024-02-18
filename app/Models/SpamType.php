<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SpamType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|SpamType newModelQuery()
 * @method static Builder|SpamType newQuery()
 * @method static Builder|SpamType query()
 * @method static Builder|SpamType whereCreatedAt($value)
 * @method static Builder|SpamType whereId($value)
 * @method static Builder|SpamType whereName($value)
 * @method static Builder|SpamType whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class SpamType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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
}
