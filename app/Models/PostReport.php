<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PostReport
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int $report_type_id
 * @property string|null $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read ReportType $reportType
 * @property-read User $user
 *
 * @method static Builder|PostReport newModelQuery()
 * @method static Builder|PostReport newQuery()
 * @method static Builder|PostReport query()
 * @method static Builder|PostReport whereCreatedAt($value)
 * @method static Builder|PostReport whereId($value)
 * @method static Builder|PostReport whereMessage($value)
 * @method static Builder|PostReport wherePostId($value)
 * @method static Builder|PostReport whereReportTypeId($value)
 * @method static Builder|PostReport whereUpdatedAt($value)
 * @method static Builder|PostReport whereUserId($value)
 *
 * @mixin Eloquent
 */
class PostReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'report_type_id',
        'message',
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

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class);
    }
}
