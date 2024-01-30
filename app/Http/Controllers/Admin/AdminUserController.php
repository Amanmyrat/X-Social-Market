<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use App\Transformers\UserListTransformer;
use App\Transformers\UserWithProfileTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends ApiBaseController
{
    public function __construct(protected UserService $service)
    {
        parent::__construct();
    }

    /**
     * Users list
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;
        $type = $request->type ?? User::TYPE_USER;

        $brands = $this->service->list($type, $limit, $query);
        return $this->respondWithPaginator($brands, new UserListTransformer($type == User::TYPE_SELLER));
    }

    /**
     * User details
     * @param User $user
     * @return JsonResponse
     */
    public function userDetails(User $user): JsonResponse
    {
        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings']),
            new UserWithProfileTransformer()
        );
    }

    /**
     * Update user
     * @param User $user
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function update(User $user, UserUpdateRequest $request): JsonResponse
    {
        $user = $this->service->updateWithProfile($user, $request->validated());
        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings']),
            new UserWithProfileTransformer(),
            'Successfully updated user'
        );
    }

    /**
     * Delete users
     * @param UserDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(UserDeleteRequest $request): JsonResponse
    {
        User::whereIn('id', $request->users)->delete();

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully deleted'
            ]
        );
    }
}
