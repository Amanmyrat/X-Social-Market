<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\UserService;
use App\Transformers\UserSimpleTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends ApiBaseController
{
    public function __construct(
        protected UserService $service
    ) {
        parent::__construct();
    }

    /**
     * Update the user password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        UserService::updatePassword($request);

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Update the user phone.
     */
    public function updatePhone(Request $request): JsonResponse
    {
        UserService::updatePhone($request);

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Create new user password.
     */
    public function newPassword(Request $request): JsonResponse
    {
        UserService::newPassword($request);

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Update the username or email of User.
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
     */
    public function delete(Request $request): JsonResponse
    {
        $request->user()->delete();

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Make account business
     */
    public function makeAccountBusiness(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $request->user()->update([
            'type' => User::TYPE_SELLER,
        ]);

        $request->user()->profile()->update($validated);

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Get all users list
     */
    public function getAll(): JsonResponse
    {
        return $this->respondWithCollection(User::latest()->get(), new UserTransformer());
    }

    /**
     * Search users
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['search_query' => ['required', 'string']]);
        $users = $this->service->search($request);

        return $this->respondWithCollection($users, new UserSimpleTransformer());
    }
}
