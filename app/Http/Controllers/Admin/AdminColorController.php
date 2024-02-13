<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\ColorCreateRequest;
use App\Http\Requests\Color\ColorDeleteRequest;
use App\Http\Requests\Color\ColorUpdateRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Services\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JetBrains\PhpStorm\Pure;

class AdminColorController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Color());
    }

    /**
     * Colors list
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $colors = $this->service->list($limit, $query);

        return ColorResource::collection($colors);
    }

    /**
     * Color details
     */
    #[Pure]
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
