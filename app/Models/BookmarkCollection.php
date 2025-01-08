<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\BookmarkCollection
 *
 * @property-read Collection<int, PostBookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read User|null $user
 * @method static Builder|BookmarkCollection newModelQuery()
 * @method static Builder|BookmarkCollection newQuery()
 * @method static Builder|BookmarkCollection query()
 * @mixin Eloquent
 */
class BookmarkCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(PostBookmark::class, 'collection_id');
    }
}
