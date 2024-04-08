<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostRatingService;
use App\Transformers\PostRatingTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostRatingController extends ApiBaseController
{
    public function __construct(protected PostRatingService $service)
    {
        parent::__construct();
    }
    /**
     * Post's ratings
     */
    public function ratings(Post $post): JsonResponse
    {
        return $this->respondWithCollection($post->ratings, new PostRatingTransformer());
    }

    /**
     * Add rating to given product
     */
    public function addRating(Post $post, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $this->service->addRating($validated, $post, Auth::user());

        return $this->respondWithMessage(trans('notification.rating_success'));
    }
}
