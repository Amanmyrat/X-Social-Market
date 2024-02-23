<?php

namespace App\Http\Resources\Admin\Location;

use App\Http\Resources\AppJsonResource;
use Illuminate\Http\Request;

class LocationResource extends AppJsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'is_active' => $this->resource->is_active,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
