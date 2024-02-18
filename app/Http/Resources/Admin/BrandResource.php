<?php

namespace App\Http\Resources\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    private bool $detailsEnabled;

    public function __construct(Brand $resource, bool $detailsEnabled = false)
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
            'title' => $this->resource->title,
            'type' => $this->resource->type,
            'is_active' => $this->resource->is_active,
            'products_count' => 0,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

        ] : [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'is_active' => $this->resource->is_active,
            'products_count' => 0,
        ];
    }
}
