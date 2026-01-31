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
    public function __construct(protected AuthService $service)
    {
        parent::__construct();
    }

    /**
     * Register user with OTP validation
     *
     * @unauthenticated
     *
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->service->register($request->validated());

            $user = array_merge($user->toArray(), ['token' => $user->createToken('mobile', ['role:user'])->plainTextToken]);

            return $this->respondWithItem(
                $user,
                new UserTransformer()
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Login user with OTP validation
     *
     * @unauthenticated
     *
     * @throws Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->service->login($request->validated());

            $user = array_merge($user->toArray(), ['token' => $user->createToken('mobile', ['role:user'])->plainTextToken]);

            return $this->respondWithItem(
                $user,
                new UserTransformer()
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
