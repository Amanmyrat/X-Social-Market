<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\LocationCreateRequest;
use App\Http\Requests\Location\LocationDeleteRequest;
use App\Http\Requests\Location\LocationUpdateRequest;
use App\Http\Resources\Admin\Location\LocationResource;
use App\Models\Location;
use App\Services\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminLocationController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Location());
    }

    /**
     * Locations list
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $locations = $this->service->list($limit, $query);

        return LocationResource::collection($locations);
    }

    /**
     * Location details
     */
    public function locationDetails(Location $location): LocationResource
    {
        return new LocationResource($location);
    }

    /**
     * Create location
     */
    public function create(LocationCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new location',
        ]);
    }

    /**
     * Update location
     */
    public function update(Location $location, LocationUpdateRequest $request): LocationResource
    {
        /** @var Location $location */
        $location = $this->service->update($location, $request->validated());

        return new LocationResource($location);

    }

    /**
     * Delete locations
     */
    public function delete(LocationDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->locations);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }
}
