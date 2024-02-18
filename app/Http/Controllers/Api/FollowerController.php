<?php

namespace App\Http\Controllers\Api;

use App\Models\Follower;
use App\Models\User;
use App\Services\FollowerService;
use App\Transformers\UserSimpleTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FollowerController extends ApiBaseController
{
    /**
     * Follow user
     */
    public function follow(Request $request): JsonResponse
    {
        $validated = $request->validate([
            //            'following_id' => ['required', 'integer', 'exists:'. User::class.',id', 'not_in:'. $request->user()->id],
            'following_id' => [
                'required',
                'integer',
                Rule::exists(User::class, 'id'),
                function ($attribute, $value, $fail) {
                    if ($value == auth('sanctum')->user()->id) {
                        $fail($attribute.' cannot be the same as your user ID.');
                    }
                },
            ],
        ]);

        FollowerService::follow($validated['following_id']);

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
        FollowerService::unfollow($validated['following_id']);

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Followers list
     */
    public function followers(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->followers, new UserSimpleTransformer(true));
    }

    /**
     * User followers list
     */
    public function userFollowers(User $user): JsonResponse
    {
        return $this->respondWithCollection($user->followers, new UserSimpleTransformer(true));
    }

    /**
     * Followings list
     */
    public function followings(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->followings, new UserSimpleTransformer());
    }

    /**
     * User followings list
     */
    public function userFollowings(User $user): JsonResponse
    {
        return $this->respondWithCollection($user->followings, new UserSimpleTransformer(true));
    }
}
