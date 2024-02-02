<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\UserProfileService;
use App\Transformers\UserWithProfileTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends ApiBaseController
{
    /**
     * Update the profile of user.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        UserProfileService::update($request);

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
