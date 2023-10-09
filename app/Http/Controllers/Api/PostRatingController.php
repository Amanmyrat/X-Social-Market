<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostRatingService;
use App\Transformers\PostRatingTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostRatingController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function ratings(Post $post): JsonResponse
    {
        return $this->respondWithCollection($post->ratings, new PostRatingTransformer());
    }

    /**
     * Add comment to given product
     */
    public function addRating(Post $post, Request $request): JsonResponse
    {
        PostRatingService::addRating($request, $post);
        return $this->respondWithMessage(trans('notification.rating_success'));
    }
}
