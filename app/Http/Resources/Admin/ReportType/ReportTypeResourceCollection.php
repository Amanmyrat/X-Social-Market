<?php

namespace App\Http\Resources\Admin\ReportType;

use App\Http\Resources\BaseCollectionResource;
use Illuminate\Http\Request;

class ReportTypeResourceCollection extends BaseCollectionResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($size) {
                return new ReportTypeResource($size, false);
            }),
        ];
    }
}
