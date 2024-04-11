<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ProfileView;
use App\Models\User;
use App\Services\UserProfileService;
use App\Transformers\UserWithProfileTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserProfileController extends ApiBaseController
{
    public function __construct(protected UserProfileService $service)
    {
        parent::__construct();
    }

    /**
     * Update the profile of user.
     *
     * @throws Throwable
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $this->service->update($request->user(), $request->validated());

        return $this->respondWithItem(
            $request->user()->loadCount(['posts', 'followers', 'followings']),
            new UserWithProfileTransformer()
        );
    }

    /**
     * Get the profile of user.
     */
    public function get(User $user): JsonResponse
    {
        if ($user->profile()->exists()) {
            $viewExists = ProfileView::where('user_profile_id', $user->profile->id)
                ->where('viewer_id', Auth::id())
                ->whereDate('viewed_at', today())
                ->exists();

            if (! $viewExists) {
                ProfileView::create([
                    'user_profile_id' => $user->profile->id,
                    'viewer_id' => Auth::id(),
                    'viewed_at' => today(),
                ]);
            }
        }

        return $this->respondWithItem(
            $user->loadCount(['posts', 'followers', 'followings'])
                ->loadAvg('ratings', 'rating'),
            new UserWithProfileTransformer(true)
        );
    }
}
