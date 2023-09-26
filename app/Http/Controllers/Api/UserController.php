<?php

namespace App\Http\Controllers\Api;

use App\Services\UserService;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends ApiBaseController
{
    /**
     * Update the user password.
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        UserService::updatePassword($request);

        return $this->respondWithArray([
            'success' => true
        ]);
    }

    /**
     * Create new user password.
     * @param Request $request
     * @return JsonResponse
     */
    public function newPassword(Request $request): JsonResponse
    {
        UserService::newPassword($request);

        return $this->respondWithArray([
            'success' => true
        ]);
    }

    /**
     * Update the username or email of User.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        UserService::update($request);

        return $this->respondWithItem(
            $request->user()->toArray(),
            new UserTransformer()
        );
    }

    /**
     * Delete user.
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $request->user()->delete();

        return $this->respondWithArray([
            'success' => true
        ]);
    }
}
