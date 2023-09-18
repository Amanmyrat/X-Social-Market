<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiBaseController
{

    /**
     * Send otp code to the given phone
     * @param Request $request
     * @return JsonResponse
     */
    public function sendRegisterOtp(Request $request): JsonResponse
    {
        $code = AuthService::sendRegisterOtp($request);

        return $this->respondWithArray([
            'success' => true,
            'data'    => [
                'code'  => $code,
            ]
        ]);

    }

    /**
     * Confirm otp code to the given phone
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmRegisterOTP(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'code' => ['required', 'integer', 'between:1000,9999'],
            ]
        );

        // TODO Add otp code check
        if ($validated['code'] != 1111){
            $this->setStatusCode(400);
            return $this->respondWithError('OTP did not match', 400);
        }

        return $this->respondWithArray([
            'success' => true,
        ]);

    }

    /**
     * Create User
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(Request $request): JsonResponse
    {
        $user = AuthService::register($request);

        return $this->respondWithArray([
            'success' => true,
            'data' => [
                'user' => array_merge($user->toArray(), ['token' => $user->createToken("API TOKEN")->plainTextToken])
            ]
        ]);
    }

    /**
     * Login user
     * @throws \Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        AuthService::login($request);

        return $this->respondWithArray([
            'success' => true,
            'data' => [
                'user' => array_merge($request->user()->toArray(), ['token' => $request->user()->createToken("API TOKEN")->plainTextToken])
            ]
        ]);
    }

    /**
     * Update the user password.
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        UserService::updatePassword($request);

        return $this->respondWithArray([
            'success' => true
        ]);
    }

    /**
     * Update the username or email of User.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        UserService::update($request);

        return $this->respondWithArray([
            'success' => true,
            'data'    => [
                'user' => $request->user()
            ]
        ]);
    }

    /**
     * Delete user.
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $request->user()->delete();

        return $this->respondWithArray([
            'success' => true
        ]);
    }
}
