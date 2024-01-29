<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Brand
 *
 * @mixin Eloquent
 * @property int id
 * @property string title
 * @property string type
 * @property bool is_active
 * @property string created_at
 * @property string updated_at
 */
class Brand extends Model
{
    use HasFactory;

    public const TYPE_SIMPLE = 'simple';
    public const TYPE_CLOTHING = 'clothing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'type',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'bool'
    ];

//    /**
//     * Get products record associated with the brand.
//     */
//    public function products(): HasMany
//    {
//        return $this->hasMany(Post::class);
//    }
}
