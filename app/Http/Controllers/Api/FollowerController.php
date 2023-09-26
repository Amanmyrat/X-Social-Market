<?php

namespace App\Http\Controllers\Api;

use App\Services\FollowerService;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowerController extends ApiBaseController
{
    /**
     * Follow user
     * @param Request $request
     * @return JsonResponse
     */
    public function follow(Request $request): JsonResponse
    {
        FollowerService::follow($request);

        return $this->respondWithArray([
            'success' => true
        ]);
    }

    /**
     * Unfollow user
     * @param Request $request
     * @return JsonResponse
     */
    public function unfollow(Request $request): JsonResponse
    {
        FollowerService::unfollow($request);

        return $this->respondWithArray([
            'success' => true
        ]);
    }

    /**
     * Followers list
     * @return JsonResponse
     */
    public function followers(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->followers, new UserTransformer());
    }

    /**
     * Followings list
     * @return JsonResponse
     */
    public function followings(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->followings, new UserTransformer());
    }
}
