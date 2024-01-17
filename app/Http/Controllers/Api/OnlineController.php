<?php

namespace App\Http\Controllers\Api;

use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Jobs\ProcessMessageSent;
use App\Jobs\ProcessUserOffline;
use App\Jobs\ProcessUserOnline;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineController extends ApiBaseController
{
    protected UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();

    }

    /**
     * @return JsonResponse
     */
    public function goOnline(): JsonResponse
    {
        $user = Auth::user();
        $this->userService->setOnlineStatus($user, true);

        return response()->json(['message' => 'User is now online', 'status' => 'success']);
    }

    /**
     * @return JsonResponse
     */
    public function goOffline(): JsonResponse
    {
        $user = Auth::user();
        $this->userService->setOnlineStatus($user, false);
        return response()->json(['message' => 'User is now offline', 'status' => 'success']);

    }

}
