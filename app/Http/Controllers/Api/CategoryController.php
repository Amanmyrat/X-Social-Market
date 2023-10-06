<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiBaseController
{
    /**
     * Create category
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function create(CategoryRequest $request): JsonResponse
    {
        CategoryService::create($request);
        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new category'
            ]
        );
    }

    /**
     * Categories list
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        return $this->respondWithCollection(Category::all(), new CategoryTransformer());
    }

}
