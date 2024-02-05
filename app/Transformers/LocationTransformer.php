<?php

namespace App\Transformers;

use App\Models\Location;
use League\Fractal\TransformerAbstract;

class LocationTransformer extends TransformerAbstract
{
    public function transform(Location $location): array
    {
        return [
            'id' => $location->id,
            'title' => $location->title,
            'is_active' => $location->is_active,
            'created_at' => $location->created_at,
            'updated_at' => $location->updated_at,
        ];
    }
}
