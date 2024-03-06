<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\ReportType
 *
 * @property int $id
 * @property string $title
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
class ReportType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
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

    public function postReports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }

    public function storyReports(): HasMany
    {
        return $this->hasMany(StoryReport::class);
    }
}
