<?php

namespace App\Http\Resources\Admin\Brand;

use App\Http\Resources\BaseCollectionResource;
use Illuminate\Http\Request;

class BrandResourceCollection extends BaseCollectionResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($brand) {
                return new BrandResource($brand, false);
            }),
        ];
    }
}
