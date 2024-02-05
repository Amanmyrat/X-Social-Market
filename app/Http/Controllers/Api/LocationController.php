<?php

namespace App\Http\Controllers\Api;

use App\Models\Location;
use App\Transformers\LocationTransformer;
use Illuminate\Http\JsonResponse;

class LocationController extends ApiBaseController
{
    /**
     * Locations list
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        return $this->respondWithCollection(Location::where('is_active', true)->get(), new LocationTransformer());

    }

}
