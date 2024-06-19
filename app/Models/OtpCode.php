<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\OtpCode
 *
 * @property int $id
 * @property string $phone
 * @property string $code
 * @property Carbon $valid_until
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|OtpCode newModelQuery()
 * @method static Builder|OtpCode newQuery()
 * @method static Builder|OtpCode query()
 * @method static Builder|OtpCode whereCode($value)
 * @method static Builder|OtpCode whereCreatedAt($value)
 * @method static Builder|OtpCode whereId($value)
 * @method static Builder|OtpCode wherePhone($value)
 * @method static Builder|OtpCode whereUpdatedAt($value)
 * @method static Builder|OtpCode whereValidUntil($value)
 *
 * @mixin Eloquent
 */
class OtpCode extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone',
        'code',
        'valid_until',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_until' => 'datetime',
    ];
}
