<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

//    public function store(Request $request): UserResource
//    {
//        $validatedData = $request->validate([
//            'phone' => 'required|unique:users',
//            'type' => 'required|in:user,service,business'
//        ]);
//        $user = $this->userService->createUser($validatedData);
//        return UserResource::make($user);
//    }
//
//    public function update(Request $request, User $user): UserResource
//    {
//        $validatedData = $request->validate([
//            'phone' => 'sometimes|required|unique:users,phone,'.$user->id,
//            'type' => 'sometimes|required|in:user,service,business'
//        ]);
//
//        $user = $this->userService->updateUser($user, $validatedData);
//        return UserResource::make($user);
//    }
//
//    public function delete(User $user): JsonResponse
//    {
//        $this->userService->deleteUser($user);
//        return response()->json(['message' => 'Deleted successfully']);
//    }
//
//    public function list(Request $request): AnonymousResourceCollection
//    {
//        $pageSize = $request->query('limit', 10);
//
//        $users = User::latest()->paginate($pageSize);
//
//        return UserResource::collection($users);
//    }
//
//    public function show(User $user): UserResource
//    {
//        return UserResource::make($user);
//    }

}
