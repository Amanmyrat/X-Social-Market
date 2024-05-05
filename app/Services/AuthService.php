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
     * @throws Exception
     */
    public function register($registerData): ?User
    {
        $user = User::create([
            'username' => 'ulanyjy_'.random_int(10000000, 99999999),
            'phone' => $registerData['phone'],
            'password' => Hash::make($registerData['password']),
            'device_token' => $registerData['device_token'],
            'last_activity' => now(),
            'type' => User::TYPE_USER,
        ]);

        $user?->profile()->create([]);

        return $user;
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): void
    {
        $request->authenticate();
        $request->user()->update(
            [
                'device_token' => $request->device_token,
                'last_activity' => now(),
            ]
        );
    }
}
