<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\UserBlockService;
use App\Transformers\UserSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockedUserController extends ApiBaseController
{
    /**
     * Follow user
     */
    public function block(Request $request): JsonResponse
    {
        UserBlockService::block($request);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Unfollow user
     */
    public function unblock(Request $request): JsonResponse
    {
        UserBlockService::unblock($request);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Blocked users list
     */
    public function blockedList(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->respondWithCollection($user->blockedUsers, new UserSimpleTransformer());
    }
}
