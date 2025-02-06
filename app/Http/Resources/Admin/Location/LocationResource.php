<?php

namespace App\Http\Resources\Admin\Location;

use App\Http\Resources\AppJsonResource;
use Illuminate\Http\Request;

class LocationResource extends AppJsonResource
{
    private bool $detailsEnabled;

    public function __construct($resource, bool $detailsEnabled = false)
    {
        parent::__construct($resource);
        $this->detailsEnabled = $detailsEnabled;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->detailsEnabled ? [
            'id' => $this->resource->id,
            'title' => json_decode($this->resource->getRawOriginal('title')),
            'is_active' => $this->resource->is_active,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ]: [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'is_active' => $this->resource->is_active,
            'created_at' => $this->resource->created_at,
        ];
    }
}
