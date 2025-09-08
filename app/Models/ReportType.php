<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\ReportType
 *
 * @property int $id
 * @property string $title
 * @property bool $is_active
 * @property bool $message_required
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ReportType newModelQuery()
 * @method static Builder|ReportType newQuery()
 * @method static Builder|ReportType query()
 * @method static Builder|ReportType whereCreatedAt($value)
 * @method static Builder|ReportType whereId($value)
 * @method static Builder|ReportType whereName($value)
 * @method static Builder|ReportType whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class ReportType extends BaseModel
{
    use HasFactory;
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'is_active',
        'message_required',
    ];

    public $translatable = ['title'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'bool',
        'message_required' => 'bool',
    ];

    public function postReports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }

    public function storyReports(): HasMany
    {
        return $this->hasMany(StoryReport::class);
    }

    public function userReports(): HasMany
    {
        return $this->hasMany(UserReport::class);
    }
}
