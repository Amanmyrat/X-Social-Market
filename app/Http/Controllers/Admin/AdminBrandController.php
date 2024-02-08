<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Brand\BrandCreateRequest;
use App\Http\Requests\Brand\BrandDeleteRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;
use App\Models\Brand;
use App\Models\Size;
use App\Services\UniversalService;
use App\Transformers\BrandTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminBrandController extends ApiBaseController
{
    public function __construct(protected UniversalService $service)
    {
        parent::__construct();
        $this->service->setModel(new Size());
    }

    /**
     * Brands list
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;
        $type = $request->type ?? Brand::TYPE_SIMPLE;

        $conditions['type'] = $type;

        $brands = $this->service->list($limit, $query, $conditions);
        return $this->respondWithPaginator($brands, new BrandTransformer(false));
    }

    /**
     * Brand details
     * @param Brand $brand
     * @return JsonResponse
     */
    public function brandDetails(Brand $brand): JsonResponse
    {
        return $this->respondWithItem($brand, new BrandTransformer(true));
    }

    /**
     * Create brand
     * @param BrandCreateRequest $request
     * @return JsonResponse
     */
    public function create(BrandCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());
        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new brand'
            ]
        );
    }

    /**
     * Update brand
     * @param Brand $brand
     * @param BrandUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Brand $brand, BrandUpdateRequest $request): JsonResponse
    {
        $brand = $this->service->update($brand, $request->validated());
        return $this->respondWithItem($brand, new BrandTransformer(true), 'Successfully updated brand');
    }

    /**
     * Delete brands
     * @param BrandDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(BrandDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->brands);

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully deleted'
            ]
        );
    }
}
