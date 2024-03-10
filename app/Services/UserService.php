<?php

namespace App\Services;

use App\Jobs\ProcessUserOffline;
use App\Jobs\ProcessUserOnline;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserService
{
    public static function updatePassword(Request $request): void
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
    }

    public static function updatePhone(Request $request): void
    {
        $validated = $request->validate(
            [
                'phone' => ['required', 'integer', 'unique:'.User::class],
            ]
        );
        $request->user()->update($validated);
    }

    public static function newPassword(Request $request): void
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
    }

    public static function update(Request $request): void
    {
        $validated = $request->validate([
            'username' => ['filled', 'string', 'min:3', 'alpha_dash', 'unique:'.User::class],
            'email' => ['filled', 'email', 'unique:'.User::class],
        ]);
        $request->user()->update($validated);
    }

    public function search(Request $request): LengthAwarePaginator
    {
        $limit = $request->get('limit');

        $users = User::with('profile')
            ->when(isset($request->search_query), function ($query) use ($request) {
                $search_query = '%'.$request->search_query.'%';

                return $query->where('username', 'LIKE', $search_query)
                    ->orWhereHas('profile', function ($query) use ($search_query) {
                        $query->where('full_name', 'LIKE', $search_query);
                    });
            });

        return $users->paginate($limit);
    }

    public function setOnlineStatus(User $user, bool $isOnline)
    {
        $user->is_online = $isOnline;
        $user->last_activity = now();
        $user->save();

        if ($isOnline) {
            ProcessUserOnline::dispatch($user);
        } else {
            ProcessUserOffline::dispatch($user);
        }
    }
}
