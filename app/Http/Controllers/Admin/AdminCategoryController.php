<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryDeleteRequest;
use App\Http\Requests\Category\CategoryListRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Resources\Admin\Category\CategoryResourceCollection;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use App\Services\Admin\UniversalService;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class AdminCategoryController extends Controller
{
    public function __construct(
        protected UniversalService $service,
        protected CategoryService $categoryService,
    ) {
        $this->service->setModel(new Category());
    }

    /**
     * Categories list
     */
    public function list(CategoryListRequest $request): CategoryResourceCollection
    {
        $validated = $request->validated();
        $limit = $validated['limit'] ?? 10;
        $query = $request->get('search_query') ?? null;
        $sort = $validated['sort'] ?? null;

        $categories = $this->service->list(
            limit: $limit,
            search_query: $query,
            relationsCount: ['posts'],
            sort: $sort
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
     *
     * @throws Throwable
     */
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $this->categoryService->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new category',
        ]);
    }

    /**
     * Update category
     *
     * @throws Exception|Throwable
     */
    public function update(Category $category, CategoryUpdateRequest $request): CategoryResource
    {
        /** @var Category $category */
        $category = $this->categoryService->update($category, $request->validated());

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
