<?php

namespace App\Http\Controllers\Api;

use App\Services\FollowerService;
use App\Transformers\UserSimpleTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowerController extends ApiBaseController
{
    /**
     * Follow user
     */
    public function follow(Request $request): JsonResponse
    {
        FollowerService::follow($request);

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Unfollow user
     */
    public function unfollow(Request $request): JsonResponse
    {
        FollowerService::unfollow($request);

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Followers list
     */
    public function followers(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->followers, new UserSimpleTransformer());
    }

    /**
     * Followings list
     */
    public function followings(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->followings, new UserSimpleTransformer());
    }
}
