<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\PostNotification

 *
 * @mixin Eloquent
 */
class PostNotification extends Model
{
    use HasFactory;

    protected $fillable = ['post_id'];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
