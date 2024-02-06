<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostFavoriteService;
use App\Transformers\PostTransformer;
use App\Transformers\UserSimpleTransformer;
use Illuminate\Http\JsonResponse;

class PostFavoritesController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function favorites(): JsonResponse
    {
        $products = PostFavoriteService::get();
        return $this->respondWithCollection($products, new PostTransformer());
    }

    /**
     * Change favorite
     */
    public function change(Post $post): JsonResponse
    {
        $message = PostFavoriteService::add($post);
        return $this->respondWithMessage($message);
    }

    /**
     * Get post favorite users
     */
    public function favoriteUsers(Post $post): JsonResponse
    {
        $users = $post->favoriteByUsers()->get();
        return $this->respondWithCollection($users, new UserSimpleTransformer());
    }
}
