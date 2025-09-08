<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserListRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use App\Transformers\UserListTransformer;
use App\Transformers\UserWithProfileTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AdminUserController extends ApiBaseController
{
    public function __construct(protected UserService $service)
    {
        parent::__construct();
    }

    /**
     * Users list
     */
    public function list(UserListRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $limit = $validated['limit'] ?? 10;
        $query = $request->get('search_query') ?? null;
        $type = $validated['type'] ?? User::TYPE_USER;
        $sort = $validated['sort'] ?? null;

        $brands = $this->service->list($type, $limit, $query, $sort);

        return $this->respondWithPaginator($brands, new UserListTransformer($type == User::TYPE_SELLER));
    }

    /**
     * User details
     */
    public function userDetails(User $user): JsonResponse
    {
        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings', 'activePosts']),
            new UserWithProfileTransformer()
        );
    }

    /**
     * Update user
     *
     * @throws Throwable
     */
    public function update(User $user, UserUpdateRequest $request): JsonResponse
    {
        $user = $this->service->updateWithProfile($user, $request->validated());

        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings', 'activePosts']),
            new UserWithProfileTransformer(),
            'Successfully updated user'
        );
    }

    /**
     * Delete users
     */
    public function delete(UserDeleteRequest $request): JsonResponse
    {
        User::whereIn('id', $request->users)->delete();

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }

    /**
     * Block user
     */
    public function blockUser(User $user, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $user->blocked_at = now();
        $user->block_reason = $request->get('reason');
        $user->save();

        return new JsonResponse([
            'success' => true,
            'message' => 'User blocked successfully',
        ]);
    }

    /**
     * Unblock user
     */
    public function unBlockUser(User $user): JsonResponse
    {
        $user->blocked_at = null;
        $user->block_reason = null;
        $user->save();

        return new JsonResponse([
            'success' => true,
            'message' => 'User unblocked successfully',
        ]);
    }
}
