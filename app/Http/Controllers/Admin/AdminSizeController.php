<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Size\SizeCreateRequest;
use App\Http\Requests\Size\SizeDeleteRequest;
use App\Http\Requests\Size\SizeUpdateRequest;
use App\Http\Resources\Admin\Size\SizeResource;
use App\Http\Resources\Admin\Size\SizeResourceCollection;
use App\Models\Size;
use App\Services\Admin\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSizeController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Size());
    }

    /**
     * Sizes list
     */
    public function list(Request $request): SizeResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $sizes = $this->service->list($limit, $query);

        return new SizeResourceCollection($sizes);
    }

    /**
     * Size details
     */
    public function sizeDetails(Size $size): SizeResource
    {
        return new SizeResource($size, true);
    }

    /**
     * Create size
     */
    public function create(SizeCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new size',
        ]);
    }

    /**
     * Update size
     */
    public function update(Size $size, SizeUpdateRequest $request): SizeResource
    {
        /** @var Size $size */
        $size = $this->service->update($size, $request->validated());

        return new SizeResource($size, true);
    }

    /**
     * Delete sizes
     */
    public function delete(SizeDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->sizes);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }
}
