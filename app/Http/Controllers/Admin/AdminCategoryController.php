<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryDeleteRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Resources\Admin\Category\CategoryResourceCollection;
use App\Models\Category;
use App\Services\Admin\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Category());
    }

    /**
     * Categories list
     */
    public function list(Request $request): CategoryResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $categories = $this->service->list(
            limit: $limit,
            search_query: $query,
            relationsCount: ['posts']
        );

        return new CategoryResourceCollection($categories);
    }

    /**
     * Category details
     */
    public function categoryDetails(Category $category): CategoryResource
    {
        return new CategoryResource($category->loadCount('posts'), true);
    }

    /**
     * Create category
     */
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new category',
        ]);
    }

    /**
     * Update category
     */
    public function update(Category $category, CategoryUpdateRequest $request): CategoryResource
    {
        /** @var Category $category */
        $category = $this->service->update($category, $request->validated());

        return new CategoryResource($category->loadCount('posts'), true);
    }

    /**
     * Delete categories
     */
    public function delete(CategoryDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->categories);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }
}
