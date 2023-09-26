<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Validation\Rules;
use Hash;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * @param Request $request
     * @return User|null
     * @throws Exception
     */
    public static function register(Request $request): User|null
    {
        $validated = $request->validate(
            [
                'phone' => ['required', 'integer', 'between:61000000,65999999', 'unique:' . User::class],
                'device_token' => ['required', 'string'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]
        );

        return User::create([
            'username' => 'ulanyjy_'.random_int(10000000, 99999999),
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'device_token' => $validated['device_token'],
            'last_login' => now(),
            'type' => User::TYPE_USER,
        ]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public static function login(Request $request): void
    {
        $request->authenticate();
        $request->user()->update(
            [
                'device_token' => $request->device_token,
                'last_login' => now()
            ]
        );
    }
}
