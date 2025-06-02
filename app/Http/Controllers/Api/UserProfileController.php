<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorMessage;
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
     * Update user profile.
     *
     * @throws Throwable
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $this->service->update($request->user(), $request->validated());

        return $this->respondWithItem(
            $request->user()->loadCount(['posts', 'followers', 'followings', 'activePosts']),
            new UserWithProfileTransformer()
        );
    }

    /**
     * Get user profile.
     */
    public function get(User $user): JsonResponse
    {
        // Check if target user has blocked current user (reverse blocking)
        abort_if(
            $user->blockedUsers->contains(Auth::user()),
            403,
            ErrorMessage::USER_BLOCKED_ERROR->value
        );

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
            $user->loadCount(['posts', 'followers', 'followings', 'activePosts'])
                ->loadAvg('ratings', 'rating'),
            new UserWithProfileTransformer(true)
        );
    }
}
