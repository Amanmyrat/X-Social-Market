<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorMessage;
use App\Http\Requests\FollowerRequest;
use App\Models\Follower;
use App\Models\User;
use App\Services\FollowerService;
use App\Transformers\UserSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowerController extends ApiBaseController
{
    public function __construct(protected FollowerService $service)
    {
        parent::__construct();
    }

    /**
     * Follow user
     */
    public function follow(FollowerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::with('profile')->firstWhere('id', $validated['following_id']);

        abort_if($user->profile?->private, 403, ErrorMessage::USER_PRIVATE_ERROR->value);

        $this->service->follow($validated['following_id'], Auth::user());

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Unfollow user
     */
    public function unfollow(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'following_id' => ['required', 'integer', 'exists:'.User::class.',id', 'exists:'.Follower::class.',following_user_id'],
            ]
        );
        $this->service->unfollow($validated['following_id'], Auth::user());

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * My Followers list
     */
    public function followers(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->respondWithCollection($user->followers, new UserSimpleTransformer(true));
    }

    /**
     * User followers list
     */
    public function userFollowers(User $user): JsonResponse
    {
        return $this->respondWithCollection($user->followers, new UserSimpleTransformer(true));
    }

    /**
     * My Followings list
     */
    public function followings(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->respondWithCollection($user->followings, new UserSimpleTransformer());
    }

    /**
     * User followings list
     */
    public function userFollowings(User $user): JsonResponse
    {
        return $this->respondWithCollection($user->followings, new UserSimpleTransformer(true));
    }
}
