<?php

namespace App\Http\Resources\Admin\Category;

use App\Http\Resources\BaseCollectionResource;
use Illuminate\Http\Request;

class CategoryResourceCollection extends BaseCollectionResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($category) {
                return new CategoryResource($category, false);
            }),
        ];
    }
}
