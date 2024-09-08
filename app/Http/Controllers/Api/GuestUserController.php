<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\UserService;
use App\Transformers\UserSimpleTransformer;
use App\Transformers\UserWithProfileTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestUserController extends ApiBaseController
{
    public function __construct(
        protected UserService $service
    ) {
        parent::__construct();
    }

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
    /**
     * Search users
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['search_query' => ['required', 'string']]);
        $validated = $request->all();
        $limit = $validated['limit'] ?? 10;

        $users =  User::with('profile')
            ->where('blocked_at', null)
            ->when(isset($validated['search_query']), function ($query) use ($validated) {
                $search_query = '%' . strtolower($validated['search_query']) . '%';

                return $query->whereRaw('LOWER(username) LIKE ?', [$search_query])
                    ->orWhereHas('profile', function ($query) use ($search_query) {
                        $query->whereRaw('LOWER(full_name) LIKE ?', [$search_query]);
                    });
            })->paginate($limit);


        return $this->respondWithPaginator($users, new UserSimpleTransformer());
    }

}
