<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\UserProfileService;
use App\Transformers\UserWithProfileTransformer;
use Illuminate\Http\JsonResponse;

class UserProfileController extends ApiBaseController
{
    /**
     * Update the profile of user.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        UserProfileService::update($request->validated(), $request->user());

        return $this->respondWithItem(
            $request->user->loadCount(['posts', 'followers', 'followings']),
            new UserWithProfileTransformer()
        );
    }

    /**
     * Get the profile of user.
     */
    public function get(User $user): JsonResponse
    {
        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings'])
                ->loadAvg('ratings', 'rating'),
            new UserWithProfileTransformer(true)
        );
    }
}
