<?php

namespace App\Http\Controllers\Api;

use App\Services\UserProfileService;
use App\Transformers\UserTransformer;
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
            $request->user()->toArray(),
            new UserTransformer()
        );
    }
}
