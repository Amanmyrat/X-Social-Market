<?php

namespace App\Http\Resources\Admin\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'description' => $this->resource->description,
            'icon' => url('uploads/categories/'.$this->resource->icon),
            'is_active' => $this->resource->is_active,
            'has_product' => $this->resource->has_product,
            'posts_count' => $this->resource->posts_count,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

        ] : [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'icon' => url('uploads/categories/'.$this->resource->icon),
            'is_active' => $this->resource->is_active,
            'posts_count' => $this->resource->posts_count,
        ];
    }
}
