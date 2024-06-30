<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\UserWithProfileTransformer;
use Illuminate\Http\JsonResponse;

class GuestUserController extends ApiBaseController
{
    /**
     * Get user profile.
     */
    public function get(User $user): JsonResponse
    {
        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings'])
                ->loadAvg('ratings', 'rating'),
            new UserWithProfileTransformer(false)
        );
    }
}
