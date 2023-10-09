<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostFavoriteService;
use App\Transformers\PostTransformer;
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
}
