<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserService
{
    public static function updatePassword(Request $request): void
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
    }

    public static function update(Request $request): void
    {
        $validated = $request->validate([
            'username' => ['filled', 'string', 'min:3', 'alpha_dash', 'unique:' . User::class],
            'email' => ['filled', 'email', 'unique:' . User::class],
        ]);
        $request->user()->update($validated);
    }
}
