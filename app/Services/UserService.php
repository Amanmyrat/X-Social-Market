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

    /**
     * Check and retrieve contacts.
     *
     * @param array $contacts
     * @param User $authUser
     * @return array
     */
    public function checkAndRetrieveContacts(array $contacts, User $authUser): array
    {
        // Retrieve all users with the given phone numbers and eager load 'profile'
        $users = User::whereIn('phone', $contacts)->with('profile')->get()->keyBy('phone');

        // Retrieve IDs of users the auth user is following for an efficient check later
        $followingUserIds = $authUser->followings()->pluck('users.id')->toArray();

        $results = collect($contacts)->map(function ($contact) use ($users, $followingUserIds) {
            $user = $users->get($contact);
            $isFollowed = false;

            if ($user) {
                // Check if the auth user is following this user
                $isFollowed = in_array($user->id, $followingUserIds);
            }

            return [
                'phone' => $contact,
                'user' => $user ? [
                    'id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->profile?->full_name,
                    'image' => $user->profile?->image_urls,
                    'isFollowing' => $isFollowed,
                ] : null,
            ];
        })->toArray();

        usort($results, function ($a, $b) {
            // If both have users or both don't, order doesn't change
            if (($a['user'] !== null && $b['user'] !== null) || ($a['user'] === null && $b['user'] === null)) {
                return 0;
            }
            // If $a has a user and $b doesn't, $a should come first
            return $a['user'] === null ? 1 : -1;
        });

        return $results;
    }
}
