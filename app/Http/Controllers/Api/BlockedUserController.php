<?php

namespace App\Http\Controllers\Api;

use App\Services\UserBlockService;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockedUserController extends ApiBaseController
{
    /**
     * Follow user
     * @param Request $request
     * @return JsonResponse
     */
    public function block(Request $request): JsonResponse
    {
        UserBlockService::block($request);

        return $this->respondWithArray([
            'success' => true
        ]);
    }

    /**
     * Unfollow user
     * @param Request $request
     * @return JsonResponse
     */
    public function unblock(Request $request): JsonResponse
    {
        UserBlockService::unblock($request);

        return $this->respondWithArray([
            'success' => true
        ]);
    }

    /**
     * Blocked users list
     * @return JsonResponse
     */
    public function blockedList(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->blockedUsers, new UserTransformer());
    }
}
