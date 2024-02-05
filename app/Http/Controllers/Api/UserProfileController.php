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
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        UserProfileService::update($request->validated(), $request->user());

        return $this->respondWithItem(
            $request->user(['posts', 'followers', 'followings']),
            new UserWithProfileTransformer()
        );
    }

    /**
     * Get the profile of user.
     * @param User $user
     * @return JsonResponse
     */
    public function get(User $user): JsonResponse
    {
        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings']),
            new UserWithProfileTransformer()
        );
    }
}
