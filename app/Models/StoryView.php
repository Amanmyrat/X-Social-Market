<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\StoryView
 *
 * @property int $id
 * @property int $user_id
 * @property int $story_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Story $story
 * @property-read User $user
 *
 * @method static Builder|StoryView newModelQuery()
 * @method static Builder|StoryView newQuery()
 * @method static Builder|StoryView query()
 * @method static Builder|StoryView whereCreatedAt($value)
 * @method static Builder|StoryView whereId($value)
 * @method static Builder|StoryView whereStoryId($value)
 * @method static Builder|StoryView whereUpdatedAt($value)
 * @method static Builder|StoryView whereUserId($value)
 *
 * @mixin Eloquent
 */
class StoryView extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'story_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
