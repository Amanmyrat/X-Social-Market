<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Post
 *
 * @mixin Eloquent
 * @property mixed caption
 * @property mixed media_type
 * @property mixed id
 * @property mixed can_comment
 * @property mixed location
 */
class Post extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'media_type',
        'caption',
        'location',
        'can_comment'
    ];

}
