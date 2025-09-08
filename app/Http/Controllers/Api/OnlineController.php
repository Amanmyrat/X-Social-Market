<?php

namespace App\Http\Controllers\Api;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OnlineController extends ApiBaseController
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();

    }

    public function goOnline(): JsonResponse
    {
        $user = Auth::user();
        $this->userService->setOnlineStatus($user, true);

        return response()->json(['message' => 'User is now online', 'status' => 'success']);
    }

    public function goOffline(): JsonResponse
    {
        $user = Auth::user();
        $this->userService->setOnlineStatus($user, false);

        return response()->json(['message' => 'User is now offline', 'status' => 'success']);

    }
}
