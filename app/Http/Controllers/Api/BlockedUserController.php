<?php

namespace App\Http\Controllers\Api;

use App\Models\BlockedUser;
use App\Models\User;
use App\Services\UserBlockService;
use App\Transformers\UserSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockedUserController extends ApiBaseController
{
    public function __construct(protected UserBlockService $service)
    {
        parent::__construct();
    }

    /**
     * Block user
     */
    public function block(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'block_user_id' => ['required', 'integer', 'exists:'.User::class.',id', 'not_in:'.Auth::id()],
        ]);
        $this->service->block($validated);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Unblock user
     */
    public function unblock(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'block_user_id' => ['required', 'integer', 'exists:'.User::class.',id', 'exists:'.BlockedUser::class.',blocked_user_id'],
            ]
        );
        $this->service->unblock($validated);

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
