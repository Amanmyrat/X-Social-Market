<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'tag_post_id',
        'dx',
        'dy',
        'text_options',
    ];

    protected $casts = [
        'text_options' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function tagPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'tag_post_id');
    }
}
