<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'brand' => [
                'id' => $this->resource->brand->id,
                'title' => $this->resource->brand->title,
            ],
            'gender' => $this->resource->gender,
            'colors' => $this->resource->colors,
            'sizes' => $this->resource->sizes,
        ];
    }
}
