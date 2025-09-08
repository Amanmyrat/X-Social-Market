<?php

namespace App\Http\Resources\Admin\Admin;

use App\Http\Resources\BaseCollectionResource;
use Illuminate\Http\Request;

class AdminResourceCollection extends BaseCollectionResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($admin) {
                return new AdminResource($admin, false);
            }),
        ];
    }
}
