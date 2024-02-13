<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Transformers\UserTransformer;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthController extends ApiBaseController
{
    /**
     * Create User
     *
     * @unauthenticated
     *
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = AuthService::register($request->validated());

        $user = array_merge($user->toArray(), ['token' => $user->createToken('mobile', ['role:user'])->plainTextToken]);

        return $this->respondWithItem(
            $user,
            new UserTransformer()
        );
    }

    /**
     * Login user
     *
     * @unauthenticated
     *
     * @throws Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        AuthService::login($request);

        $user = array_merge($request->user()->toArray(), ['token' => $request->user()->createToken('mobile', ['role:user'])->plainTextToken]);

        return $this->respondWithItem(
            $user,
            new UserTransformer()
        );
    }
}
