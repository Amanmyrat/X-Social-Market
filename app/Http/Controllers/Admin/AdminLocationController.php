<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Location\LocationCreateRequest;
use App\Http\Requests\Location\LocationDeleteRequest;
use App\Http\Requests\Location\LocationUpdateRequest;
use App\Models\Location;
use App\Services\LocationService;
use App\Transformers\LocationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLocationController extends ApiBaseController
{
    public function __construct(protected LocationService $service)
    {
        parent::__construct();
    }

    /**
     * Locations list
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $locations = $this->service->list($limit, $query);
        return $this->respondWithPaginator($locations, new LocationTransformer());
    }

    /**
     * Location details
     * @param Location $location
     * @return JsonResponse
     */
    public function locationDetails(Location $location): JsonResponse
    {
        return $this->respondWithItem($location, new LocationTransformer());
    }

    /**
     * Create location
     * @param LocationCreateRequest $request
     * @return JsonResponse
     */
    public function create(LocationCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());
        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new location'
            ]
        );
    }

    /**
     * Update location
     * @param Location $location
     * @param LocationUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Location $location, LocationUpdateRequest $request): JsonResponse
    {
        $location = $this->service->update($location, $request->validated());
        return $this->respondWithItem($location, new LocationTransformer(true), 'Successfully updated location');
    }

    /**
     * Delete locations
     * @param LocationDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(LocationDeleteRequest $request): JsonResponse
    {
        Location::whereIn('id', $request->locations)->delete();

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully deleted'
            ]
        );
    }
}
