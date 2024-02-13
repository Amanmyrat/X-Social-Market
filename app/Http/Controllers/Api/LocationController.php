<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationController extends ApiBaseController
{
    /**
     * Locations list
     */
    public function list(): AnonymousResourceCollection
    {
        $locations = Location::where('is_active', true)->get();

        return LocationResource::collection($locations);
    }
}
