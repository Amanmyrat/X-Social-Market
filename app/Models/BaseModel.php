<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BaseModel
 *
 * @mixin Eloquent
 */
class BaseModel extends Model
{
    /**
     * Get the created_at attribute.
     *
     * @param string $value
     * @return string
     */
    public function getCreatedAtAttribute(string $value): string
    {
        return Carbon::parse($value)->toDateTimeString();
    }

    /**
     * Get the updated_at attribute.
     *
     * @param string $value
     * @return string
     */
    public function getUpdatedAtAttribute(string $value): string
    {
        return Carbon::parse($value)->toDateTimeString();
    }
}
