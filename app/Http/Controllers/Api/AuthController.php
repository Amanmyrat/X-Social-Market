<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Transformers\UserTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiBaseController
{
    /**
     * Create User
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(Request $request): JsonResponse
    {
        $user = AuthService::register($request);

        $user = array_merge($user->toArray(), ['token' => $user->createToken("API TOKEN")->plainTextToken]);
        return $this->respondWithItem(
            $user,
            new UserTransformer()
        );
    }

    /**
     * Login user
     * @throws Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        AuthService::login($request);

        $user = array_merge($request->user()->toArray(), ['token' => $request->user()->createToken("API TOKEN")->plainTextToken]);
        return $this->respondWithItem(
            $user,
            new UserTransformer()
        );
    }

}
