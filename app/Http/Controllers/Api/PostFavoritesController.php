<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostFavoriteService;
use App\Traits\HandlesUserPostInteractions;
use App\Traits\PreparesPostQuery;
use App\Transformers\PostTransformer;
use App\Transformers\UserSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class PostFavoritesController extends ApiBaseController
{
    use HandlesUserPostInteractions, PreparesPostQuery;

    public function __construct(protected PostFavoriteService $service)
    {
        parent::__construct();
    }

    /**
     * List of my favorite posts
     */
    public function favorites(): JsonResponse
    {
        $user = Auth::user();
        $posts = $this->service->getUserFavoritePosts($user);
        $userInteractionsDTO = $this->getUserInteractionsDTO();

        return $this->respondWithCollection($posts, new PostTransformer($userInteractionsDTO));
    }

    /**
     * Change favorite
     */
    public function change(Post $post): JsonResponse
    {
        $message = $this->service->add($post);

        return $this->respondWithMessage($message);
    }

    /**
     * Get posts' favorited users
     */
    public function favoriteUsers(Post $post): JsonResponse
    {
        $users = $post->favoriteByUsers()->get();

        return $this->respondWithCollection($users, new UserSimpleTransformer());
    }
}
