<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\ColorCreateRequest;
use App\Http\Requests\Color\ColorDeleteRequest;
use App\Http\Requests\Color\ColorUpdateRequest;
use App\Http\Resources\Admin\Color\ColorResource;
use App\Http\Resources\Admin\Color\ColorResourceCollection;
use App\Models\Color;
use App\Services\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminColorController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Color());
    }

    /**
     * Colors list
     */
    public function list(Request $request): ColorResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $colors = $this->service->list($limit, $query);

        return new ColorResourceCollection($colors);
    }

    /**
     * Color details
     */
    public function colorDetails(Color $color): ColorResource
    {
        return new ColorResource($color, true);
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

        return new ColorResource($color, true);
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
