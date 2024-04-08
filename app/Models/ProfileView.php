<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProfileView
 *
 * @property int $id
 * @property int $viewer_id
 * @property int $user_profile_id
 * @property Carbon|null $viewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read UserProfile $userProfile
 * @property-read User $viewer
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
class ProfileView extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'viewer_id',
        'user_profile_id',
        'viewed_at',
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

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userProfile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class);
    }
}
