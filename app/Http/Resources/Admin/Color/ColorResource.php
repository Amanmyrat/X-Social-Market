<?php

namespace App\Http\Resources\Admin\Color;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
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
            'title' => $this->resource->title,
            'code' => $this->resource->code,
            'is_active' => $this->resource->is_active,
            'products_count' => $this->resource->products_count,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

        ] : [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'code' => $this->resource->code,
            'is_active' => $this->resource->is_active,
            'products_count' => $this->resource->products_count,
            'created_at' => $this->resource->created_at,
        ];
    }
}
