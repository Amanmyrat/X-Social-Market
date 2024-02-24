<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorMessage;
use App\Http\Requests\FollowerRequest;
use App\Models\FollowRequest;
use App\Models\User;
use App\Services\FollowerService;
use App\Transformers\UserSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class FollowerRequestController extends ApiBaseController
{
    public function __construct(protected FollowerService $service)
    {
        parent::__construct();
    }

    /**
     * Follow request user
     */
    public function followRequest(FollowerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $exists = FollowRequest::where('followed_user_id', Auth::id())
            ->where('following_user_id', $validated['following_id'])
            ->exists();
        abort_if($exists, 403, 'record_exists');

        $user = User::with('profile')->firstWhere('id', $validated['following_id']);
        abort_if((! $user->profile?->exists() || ! $user->profile->private), 403, ErrorMessage::USER_PRIVATE_ERROR->value);

        $this->service->followRequest($validated['following_id'], Auth::user());

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * My outgoing follow requests list
     */
    public function followerRequests(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->respondWithCollection($user->outgoingRequests, new UserSimpleTransformer());
    }

    /**
     * My incoming follow requests list
     */
    public function followingRequests(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->respondWithCollection($user->incomingRequests, new UserSimpleTransformer());
    }

    /**
     * Accept follow request user
     */
    public function accept(User $user): JsonResponse
    {
        $followingRequest = FollowRequest::where('followed_user_id', Auth::id())
            ->where('following_user_id', $user->id)->first();

        abort_if(! $followingRequest, 404, 'Not found');

        $this->service->follow($user->id, Auth::user());

        $followingRequest->delete();

        return $this->respondWithArray([
            'success' => true,
        ]);
    }

    /**
     * Decline follow request user
     */
    public function decline(User $user): JsonResponse
    {
        $followingRequest = FollowRequest::where('followed_user_id', Auth::id())
            ->where('following_user_id', $user->id)->first();

        abort_if(! $followingRequest, 404, 'Not found');

        $followingRequest->delete();

        return $this->respondWithArray([
            'success' => true,
        ]);
    }
}
