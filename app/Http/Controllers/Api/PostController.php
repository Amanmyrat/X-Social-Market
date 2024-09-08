<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorMessage;
use App\Http\Requests\Post\PostFilterRequest;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
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
use App\Transformers\PostTransformer2;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * Create product
     *
     * @throws Throwable
     */
    public function createProduct(ProductRequest $request): JsonResponse
    {
        abort_if(
            Auth::user()->type != User::TYPE_SELLER,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );

        $validated = $request->validated();

        try {
            $post = $this->service->createProduct($validated, Auth::user()->id);

            $userInteractionsDTO = $this->getUserInteractionsDTO();

            $post->load(['user.profile', 'media', 'product'])
                ->loadAvg('ratings', 'rating')
                ->loadCount(['favorites', 'comments', 'views']);

            return $this->respondWithItem($post, new PostDetailsTransformer($userInteractionsDTO, []));

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    /**
     * Create post
     *
     * @throws Throwable
     */
    public function createPost(PostRequest $request): JsonResponse
    {
        abort_if(
            Auth::user()->type != User::TYPE_SELLER,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );

        $validated = $request->validated();

        try {
            $post = $this->service->createPost($validated, Auth::user()->id);

            $userInteractionsDTO = $this->getUserInteractionsDTO();

            $post->load(['user.profile', 'media', 'product'])
                ->loadAvg('ratings', 'rating')
                ->loadCount(['favorites', 'comments', 'views']);

            return $this->respondWithItem($post, new PostDetailsTransformer($userInteractionsDTO, []));

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    /**
     * Update product
     *
     * @throws Throwable
     */
    public function updateProduct(Post $post, ProductUpdateRequest $request): JsonResponse
    {
        abort_if(
            Auth::id() != $post->user_id,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );

        $validated = $request->validated();

        try {
            $post = $this->service->updateProduct($post, $validated);

            $userInteractionsDTO = $this->getUserInteractionsDTO();

            $post->load(['user.profile', 'media', 'product'])
                ->loadAvg('ratings', 'rating')
                ->loadCount(['favorites', 'comments', 'views']);

            return $this->respondWithItem($post, new PostDetailsTransformer($userInteractionsDTO, []));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Update post
     *
     * @throws Throwable
     */
    public function updatePost(Post $post, PostUpdateRequest $request): JsonResponse
    {
        abort_if(
            Auth::id() != $post->user_id,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );

        $validated = $request->validated();

        try {
            $post = $this->service->updatePost($post, $validated);

            $userInteractionsDTO = $this->getUserInteractionsDTO();

            $post->load(['user.profile', 'media', 'product'])
                ->loadAvg('ratings', 'rating')
                ->loadCount(['favorites', 'comments', 'views']);

            return $this->respondWithItem($post, new PostDetailsTransformer($userInteractionsDTO, []));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete post
     */
    public function delete(Post $post): JsonResponse
    {
        abort_if(
            Auth::id() != $post->user_id,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );
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
            ->limit(4)
            ->get();

        return $this->respondWithCollection($posts, new PostSimpleTransformer());
    }

    /**
     * My posts list
     */
    public function myPosts(): JsonResponse
    {
        abort_if(
            Auth::user()->type !== User::TYPE_SELLER,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );

        $user = Auth::user();

        $posts = $user->posts()
            ->latest()
            ->paginate(15);

        return $this->respondWithPaginator($posts, new PostSimpleTransformer());
    }

    /**
     * Recommended posts
     */
    public function recommendedPosts(): JsonResponse
    {
        $userInteractionsDTO = $this->getUserInteractionsDTO();
        $followings = $this->getUserFollowingsIds();
        $storyViewUsers = $this->getUserStoryViewUserIds();

        $posts = Post::withRecommendationScore2(Auth::id())->paginate(15);

        return $this->respondWithPaginator($posts, new PostTransformer2($userInteractionsDTO, $followings, $storyViewUsers));
    }

    /**
     * Recommended posts 3
     */
    public function recommendedPosts3(): JsonResponse
    {
        $user = Auth::user();
        $postsQuery = Post::withRecommendationScore2($user->id)->limit(1000)->get();

        $postIds = $postsQuery->pluck('id');

        // Use the postViews relationship to get all viewed post IDs for the user
        $viewedPostIds = $user->postViews()
            ->whereIn('post_views.post_id', $postIds)
            ->pluck('post_views.post_id')
            ->toArray();

        // Use the followings relationship to get all following user IDs for the user
        $followingUserIds = $user->followings()
            ->pluck('users.id')
            ->toArray();

        foreach ($postsQuery as $post) {
            $post->score = $post->common_score
                + (in_array($post->user_id, $followingUserIds) ? 100 : 0)
                + (!in_array($post->id, $viewedPostIds) ? 50 : 0);
        }

        // Sort posts by score in descending order
        $sortedPosts = $postsQuery->sortByDesc('score')->values();

        // Transform the sorted posts
        $userInteractionsDTO = $this->getUserInteractionsDTO();
        $followings = $this->getUserFollowingsIds();
        $storyViewUsers = $this->getUserStoryViewUserIds();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentItems = $sortedPosts->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedPosts = new LengthAwarePaginator($currentItems, $sortedPosts->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return $this->respondWithPaginator($paginatedPosts, new PostTransformer2($userInteractionsDTO, $followings, $storyViewUsers));
    }

    /**
     * Recommended posts 2
     */
    public function recommendedPosts2(): JsonResponse
    {
        $userInteractionsDTO = $this->getUserInteractionsDTO();

        $posts = Post::withRecommendationScore(Auth::id())->paginate(15);

        return $this->respondWithPaginator($posts, new PostTransformer($userInteractionsDTO));
    }

    /**
     * Guest All posts list
     */
    public function guestAllPosts(): JsonResponse
    {
        $postsQuery = $this->getPostsQuery();
        $posts = $postsQuery->inRandomOrder()->paginate(15);

        return $this->respondWithPaginator($posts, new GuestPostTransformer());
    }

    /**
     * User posts list
     */
    public function userPosts(User $user): JsonResponse
    {
        $postsQuery = $this->getUserPostsQuery($user);
        $posts = $postsQuery->paginate(15);

        return $this->respondWithPaginator($posts, new PostSimpleTransformer());
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
        $posts = $this->service->filter($filters)->activeAndNotBlocked(Auth::id())->with('media')->paginate(20);

        return $this->respondWithPaginator($posts, new PostSimpleTransformer());
    }

    /**
     * Post details
     */
    public function postDetails(Post $post): JsonResponse
    {
        $userInteractionsDTO = $this->getUserInteractionsDTO();
        $followings = $this->getUserFollowingsIds();

        $post = Post::where('posts.id', $post->id)
            ->with(['user.profile', 'media', 'product'])
            ->withAvg('ratings', 'rating')
            ->withCount(['favorites', 'activeComments', 'views'])
            ->first();

        return $this->respondWithItem($post, new PostDetailsTransformer($userInteractionsDTO, $followings));
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
