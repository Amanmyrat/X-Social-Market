<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Post\PostFilterRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use App\Traits\HandlesUserPostInteractions;
use App\Traits\PreparesPostQuery;
use App\Transformers\CommentTransformer;
use App\Transformers\GuestPostTransformer;
use App\Transformers\PostDetailsTransformer;
use App\Transformers\PostSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestPostController extends ApiBaseController
{
    use HandlesUserPostInteractions, PreparesPostQuery;

    public function __construct(protected PostService $service)
    {
        parent::__construct();
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
     * Guest all posts list
     */
    public function allPosts(): JsonResponse
    {
        $postsQuery = $this->getPostsQuery();
        $posts = $postsQuery->inRandomOrder()->paginate(15);

        return $this->respondWithPaginator($posts, new GuestPostTransformer());
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

        $post = Post::where('posts.id', $post->id)
            ->with(['user.profile', 'media', 'product', 'tags.tagPost'])
            ->withAvg('ratings', 'rating')
            ->withCount(['favorites', 'comments', 'activeComments', 'views'])
            ->first();

        return $this->respondWithItem($post, new PostDetailsTransformer($userInteractionsDTO, []));
    }

    /**
     * Post comments
     */
    public function comments(Post $post): JsonResponse
    {
        $comments = $post->activeComments()->whereNull('blocked_at')->get();

        return $this->respondWithCollection($comments, new CommentTransformer());
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
     * User products list
     */
    public function userProducts(User $user): JsonResponse
    {
        $postsQuery = $this->getUserProductsQuery($user);
        $posts = $postsQuery->paginate(15);

        return $this->respondWithPaginator($posts, new PostSimpleTransformer());
    }

}
