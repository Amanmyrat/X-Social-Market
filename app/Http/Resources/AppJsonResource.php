<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppJsonResource extends JsonResource
{
    /**
     * Indicates if the collection keys should be preserved.
     */
    public bool $preserveKeys = false;

    public static function collection($resource)
    {
        return tap(new AppAnonymousResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
