<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FractalSerializer;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Services\PostService;
use App\Transformers\PostSimpleTransformer;
use App\Transformers\PostTransformer;
use App\Transformers\UserPostTransformer;
use Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use Response;
use Throwable;

class PostController extends ApiBaseController
{
    public function __construct(
        protected PostService $service
    ) {
        $this->fractal = new Manager;
        $this->fractal->setSerializer(new FractalSerializer());
    }

    /**
     * Create post
     *
     * @throws Throwable
     */
    public function create(PostRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $productData = Arr::only($validated, 'product')['product'] ?? [];

        $postCreated = $this->service->create(Arr::except($validated, 'product'), $request->user()->id, $productData);

        if (! $postCreated) {
            return Response::json([
                'success' => false,
                'message' => 'Error occurred',
            ], 400);
        }

        return Response::json([
            'success' => true,
            'message' => 'Successfully created a new post',
        ]);
    }

    /**
     * Delete post
     */
    public function delete(Post $post): JsonResponse
    {
        $post->delete();

        return $this->respondWithMessage('Successfully deleted');
    }

    /**
     * Get related posts
     */
    public function relatedPosts(Post $post): JsonResponse
    {
        $posts = Post::where('id', '!=', $post->id)->where('category_id', $post->category_id)->inRandomOrder()->limit(10)->get();

        return $this->respondWithCollection($posts, new PostSimpleTransformer());
    }

    /**
     * My posts list
     */
    public function myPosts(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->posts, new PostTransformer());
    }

    /**
     * All posts list
     */
    public function allPosts(): JsonResponse
    {
        $posts = Post::with('product')->withCount(['favorites', 'comments', 'views'])->withIsFollowing()->latest()->paginate(10);

        return $this->respondWithPaginator($posts, new PostTransformer());
    }

    /**
     * User posts list
     */
    public function userPosts($user_id): JsonResponse
    {
        $posts = Post::where('posts.user_id', $user_id)
            ->with('product')
            ->withCount(['favorites', 'comments', 'views'])
            ->withIsFollowing()
            ->latest()->paginate(10);

        return $this->respondWithCollection($posts, new PostTransformer());
    }

    /**
     * Following users posts list
     */
    public function followingPosts(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->followings, new UserPostTransformer());
    }

    /**
     * Search posts
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['search_query' => ['required', 'string']]);

        $result = $this->service->searchPosts($request);

        return $this->respondWithPaginator($result, new PostTransformer());
    }

    /**
     * Post details
     */
    public function postDetails(Post $post): JsonResponse
    {
        $post = Post::firstWhere('id', $post->id)
            ->with('product')
            ->withCount(['favorites', 'comments', 'views'])->withIsFollowing()->get()->first();

        return $this->respondWithItem($post, new PostTransformer());
    }
}
