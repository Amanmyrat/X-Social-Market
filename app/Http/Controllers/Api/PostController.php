<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Post\PostFilterRequest;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use App\Traits\HandlesUserPostInteractions;
use App\Traits\PreparesPostQuery;
use App\Transformers\GuestPostTransformer;
use App\Transformers\PostDetailsTransformer;
use App\Transformers\PostSimpleTransformer;
use App\Transformers\PostTransformer;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;
use Throwable;

class PostController extends ApiBaseController
{
    use HandlesUserPostInteractions, PreparesPostQuery;

    public function __construct(
        protected PostService $service
    ) {
        parent::__construct();
    }

    /**
     * Create post
     *
     * @throws Throwable
     */
    public function create(PostRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->create($validated, $request->user()->id);

            return Response::json([
                'success' => true,
                'message' => 'Successfully created a new post',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    /**
     * Update post
     *
     * @throws Throwable
     */
    public function update(Post $post, PostRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->update($post, $validated);

            return Response::json([
                'success' => true,
                'message' => 'Successfully updated',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
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
        $posts = Post::with('media')
            ->where('posts.id', '!=', $post->id)
            ->where('posts.category_id', $post->category_id)
            ->activeAndNotBlocked(Auth::id())
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return $this->respondWithCollection($posts, new PostSimpleTransformer());
    }

    /**
     * My posts list
     */
    public function myPosts(): JsonResponse
    {
        $userInteractionsDTO = $this->getUserInteractionsDTO();

        $user = Auth::user();
        $postsQuery = $this->getUserPostsQuery($user);
        $posts = $postsQuery->paginate(10);

        return $this->respondWithPaginator($posts, new PostTransformer($userInteractionsDTO));
    }

    /**
     * All posts list
     */
    public function allPosts(): JsonResponse
    {
        $userInteractionsDTO = $this->getUserInteractionsDTO();

        $posts = Post::withRecommendationScore(Auth::id())->paginate(10);

        return $this->respondWithPaginator($posts, new PostTransformer($userInteractionsDTO));
    }

    /**
     * Guest All posts list
     */
    public function guestAllPosts(): JsonResponse
    {
        $postsQuery = $this->getPostsQuery();
        $posts = $postsQuery->inRandomOrder()->paginate(10);

        return $this->respondWithPaginator($posts, new GuestPostTransformer());
    }

    /**
     * User posts list
     */
    public function userPosts(User $user): JsonResponse
    {
        $userInteractionsDTO = $this->getUserInteractionsDTO();

        $postsQuery = $this->getUserPostsQuery($user);
        $posts = $postsQuery->paginate(10);

        return $this->respondWithCollection($posts, new PostTransformer($userInteractionsDTO));
    }

    /**
     * Search posts
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['search_query' => ['required', 'string']]);

        $result = $this->service->searchPosts($request);

        return $this->respondWithPaginator($result, new PostSimpleTransformer());
    }

    /**
     * Filter posts
     */
    public function filter(PostFilterRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $posts = $this->service->filter($filters)->with('media')->paginate(20);

        return $this->respondWithPaginator($posts, new PostSimpleTransformer());
    }

    /**
     * Post details
     */
    public function postDetails(Post $post): JsonResponse
    {
        $userInteractionsDTO = $this->getUserInteractionsDTO();
        $post = Post::where('posts.id', $post->id)
            ->with(['user.profile', 'media', 'product'])
            ->withAvg('ratings', 'rating')
            ->withCount(['favorites', 'comments', 'views'])
            ->withIsFollowing()
            ->first();

        return $this->respondWithItem($post, new PostDetailsTransformer($userInteractionsDTO));
    }

    /**
     * Get discovery posts
     */
    public function discoveryPosts(): JsonResponse
    {
        $posts = Post::activeAndNotBlocked(Auth::id())->with('media')->inRandomOrder()->paginate(25);

        return $this->respondWithPaginator($posts, new PostSimpleTransformer());
    }

    /**
     * Get category related posts
     */
    public function categoryPosts(Category $category): JsonResponse
    {
        $posts = Post::with(['media'])
            ->where('posts.category_id', $category->id)
            ->activeAndNotBlocked(Auth::id())
            ->inRandomOrder()
            ->paginate(20);

        return $this->respondWithPaginator($posts, new PostSimpleTransformer());
    }
}
