<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PostRequest;
use App\Services\PostService;
use App\Transformers\PostTransformer;
use Illuminate\Http\JsonResponse;

class PostController extends ApiBaseController
{
    /**
     * Create post
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function create(PostRequest $request): JsonResponse
    {
        PostService::create($request);

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new post'
            ]
        );
    }

    /**
     * My posts list
     * @return JsonResponse
     */
    public function myPosts(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->posts, new PostTransformer());
    }
}
