<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandCreateRequest;
use App\Http\Requests\Brand\BrandDeleteRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\Size;
use App\Services\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JetBrains\PhpStorm\Pure;

class AdminBrandController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Size());
    }

    /**
     * Brands list
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;
        $type = $request->type ?? Brand::TYPE_SIMPLE;

        $conditions['type'] = $type;

        $brands = $this->service->list($limit, $query, $conditions);
        return BrandResource::collection($brands);
    }

    /**
     * Brand details
     * @param Brand $brand
     * @return BrandResource
     */
    #[Pure]
    public function brandDetails(Brand $brand): BrandResource
    {
        return new BrandResource($brand, true);
    }

    /**
     * Create brand
     * @param BrandCreateRequest $request
     * @return JsonResponse
     */
    public function create(BrandCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new brand'
        ]);
    }

    /**
     * Update brand
     * @param Brand $brand
     * @param BrandUpdateRequest $request
     * @return BrandResource
     */
    public function update(Brand $brand, BrandUpdateRequest $request): BrandResource
    {
        /** @var Brand $brand */
        $brand = $this->service->update($brand, $request->validated());
        return new BrandResource($brand, true);
    }

    /**
     * Delete brands
     * @param BrandDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(BrandDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->brands);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted'
        ]);
    }
}
