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
 * @property int $reported_user_id
 * @property int $report_type_id
 * @property string|null $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ReportType $reportType
 * @property-read User $user
 * @property-read User $reportUser
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
class UserReport extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'reported_user_id',
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

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to the user who is reported
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class);
    }
}
