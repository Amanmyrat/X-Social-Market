<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostBookmarkService;
use App\Transformers\PostTransformer;
use Illuminate\Http\JsonResponse;

class PostBookmarkController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function bookmarks(): JsonResponse
    {
        $products = PostBookmarkService::get();
        return $this->respondWithCollection($products, new PostTransformer());
    }

    /**
     * Change bookmark
     */
    public function change(Post $post): JsonResponse
    {
        $message = PostBookmarkService::add($post);
        return $this->respondWithMessage($message);
    }
}
