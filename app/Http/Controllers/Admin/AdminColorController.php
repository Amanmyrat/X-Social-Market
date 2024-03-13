<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\ColorCreateRequest;
use App\Http\Requests\Color\ColorDeleteRequest;
use App\Http\Requests\Color\ColorListRequest;
use App\Http\Requests\Color\ColorUpdateRequest;
use App\Http\Resources\Admin\Color\ColorResource;
use App\Http\Resources\Admin\Color\ColorResourceCollection;
use App\Models\Color;
use App\Services\Admin\UniversalService;
use Illuminate\Http\JsonResponse;

class AdminColorController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Color());
    }

    /**
     * Colors list
     */
    public function list(ColorListRequest $request): ColorResourceCollection
    {
        $validated = $request->validated();
        $limit = $validated['limit'] ?? 10;
        $query = $validated['search_query'] ?? null;
        $sort = $validated['sort'] ?? null;

        $colors = $this->service->list(limit: $limit, search_query: $query, relationsCount: ['products'], sort: $sort);

        return new ColorResourceCollection($colors);
    }

    /**
     * Color details
     */
    public function colorDetails(Color $color): ColorResource
    {
        return new ColorResource($color->loadCount('products'), true);
    }

    /**
     * Create color
     */
    public function create(ColorCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new color',
        ]);
    }

    /**
     * Update color
     */
    public function update(Color $color, ColorUpdateRequest $request): ColorResource
    {
        /** @var Color $color */
        $color = $this->service->update($color, $request->validated());

        return new ColorResource($color->loadCount('products'), true);
    }

    /**
     * Delete colors
     */
    public function delete(ColorDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->colors);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }
}
