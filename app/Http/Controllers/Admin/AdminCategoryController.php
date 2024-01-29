<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryDeleteRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\CategoryService;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCategoryController extends ApiBaseController
{
    public function __construct(protected CategoryService $service)
    {
        parent::__construct();
    }

    /**
     * Categories list
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $categories = $this->service->list($limit, $query);
        return $this->respondWithPaginator($categories, new CategoryTransformer(false));
    }

    /**
     * Category details
     * @param Category $category
     * @return JsonResponse
     */
    public function categoryDetails(Category $category): JsonResponse
    {
        return $this->respondWithItem($category->loadCount('posts'), new CategoryTransformer(true));
    }

    /**
     * Create category
     * @param CategoryCreateRequest $request
     * @return JsonResponse
     */
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());
        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new category'
            ]
        );
    }

    /**
     * Update category
     * @param Category $category
     * @param CategoryUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Category $category, CategoryUpdateRequest $request): JsonResponse
    {
        $category = $this->service->update($category, $request->validated());
        return $this->respondWithItem($category->loadCount('posts'), new CategoryTransformer(true), 'Successfully updated category');
    }

    /**
     * Delete categories
     * @param CategoryDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(CategoryDeleteRequest $request): JsonResponse
    {
        Category::whereIn('id', $request->categories)->delete();

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully deleted'
            ]
        );
    }
}
