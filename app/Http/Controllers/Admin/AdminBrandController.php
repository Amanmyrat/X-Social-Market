<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandCreateRequest;
use App\Http\Requests\Brand\BrandDeleteRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;
use App\Http\Resources\Admin\Brand\BrandResource;
use App\Http\Resources\Admin\Brand\BrandResourceCollection;
use App\Models\Brand;
use App\Services\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminBrandController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Brand());
    }

    /**
     * Brands list
     */
    public function list(Request $request): BrandResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;
        $type = $request->type ?? Brand::TYPE_SIMPLE;

        $conditions['type'] = $type;

        $brands = $this->service->list(
            limit: $limit,
            search_query: $query,
            conditions: $conditions,
            relationsCount: ['products']
        );

        return new BrandResourceCollection($brands);
    }

    /**
     * Brand details
     */
    public function brandDetails(Brand $brand): BrandResource
    {
        return new BrandResource($brand, true);
    }

    /**
     * Create brand
     */
    public function create(BrandCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new brand',
        ]);
    }

    /**
     * Update brand
     */
    public function update(Brand $brand, BrandUpdateRequest $request): BrandResource
    {
        /** @var Brand $brand */
        $brand = $this->service->update($brand, $request->validated());

        return new BrandResource($brand, true);
    }

    /**
     * Delete brands
     */
    public function delete(BrandDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->brands);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }
}
