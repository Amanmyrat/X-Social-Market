<?php

namespace App\Http\Resources\Admin\Post;

use App\Http\Resources\BaseCollectionResource;
use Illuminate\Http\Request;

class PostResourceCollection extends BaseCollectionResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($size) {
                return new PostResource($size, false);
            }),
        ];
    }
}
