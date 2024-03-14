<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\PostDeleteRequest;
use App\Http\Requests\Post\PostListRequest;
use App\Http\Resources\Admin\Post\PostResource;
use App\Http\Resources\Admin\Post\PostResourceCollection;
use App\Models\Post;
use App\Services\Admin\PostService;
use Illuminate\Http\JsonResponse;

class AdminPostController extends Controller
{
    public function __construct(protected PostService $service)
    {
    }

    /**
     * Posts list
     */
    public function list(PostListRequest $request): PostResourceCollection
    {
        $validated = $request->validated();
        $limit = $validated['limit'] ?? 10;
        $query = $request->get('search_query') ?? null;
        $sort = $validated['sort'] ?? null;

        $posts = $this->service->list($limit, $query, $sort);

        return new PostResourceCollection($posts);
    }

    /**
     * Post details
     */
    public function postDetails(Post $post): PostResource
    {
        $postDetails = $post->load(['user', 'media', 'product', 'category'])
            ->loadCount(['favorites', 'comments', 'views'])
            ->loadAvg('ratings', 'rating');

        return new PostResource($postDetails, true);
    }

    /**
     * Delete posts
     */
    public function delete(PostDeleteRequest $request): JsonResponse
    {
        Post::whereIn('id', $request->posts)->delete();

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }
}
