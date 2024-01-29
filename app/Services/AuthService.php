<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;
use Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @param $registerData
     * @return User|null
     * @throws Exception
     */
    public static function register($registerData): User|null
    {
        return User::create([
            'username' => 'ulanyjy_'.random_int(10000000, 99999999),
            'phone' => $registerData['phone'],
            'password' => Hash::make($registerData['password']),
            'device_token' => $registerData['device_token'],
            'last_activity' => now(),
            'type' => User::TYPE_USER,
        ]);
    }

    /**
     * @param LoginRequest $request
     * @return void
     * @throws ValidationException
     */
    public static function login(LoginRequest $request): void
    {
        $request->authenticate();
        $request->user()->update(
            [
                'device_token' => $request->device_token,
                'last_activity' => now()
            ]
        );
    }
}
