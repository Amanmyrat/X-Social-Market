<?php

namespace App\Http\Resources\Admin\Location;

use App\Http\Resources\BaseCollectionResource;
use Illuminate\Http\Request;

class LocationResourceCollection extends BaseCollectionResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($color) {
                return new LocationResource($color, false);
            }),
        ];
    }
}
