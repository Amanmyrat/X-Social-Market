<?php

namespace App\Services;

use App\Jobs\ProcessUserOffline;
use App\Jobs\ProcessUserOnline;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function updatePassword(array $validated, User $user): void
    {
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
    }

    public function updatePhone(array $validated, User $user): void
    {
        $user->update($validated);
    }

    public function newPassword(array $validated, User $user): void
    {
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
    }

    public function search(array $validated): LengthAwarePaginator
    {
        $limit = $validated['limit'] ?? 10;

        $users = User::with('profile')
            ->when(isset($validated['search_query']), function ($query) use ($validated) {
                $search_query = '%'.strtolower($validated['search_query']).'%';

                return $query->whereRaw('LOWER(username) LIKE ?' ,[$search_query])
                    ->orWhereHas('profile', function ($query) use ($search_query) {
                        $query->whereRaw('LOWER(full_name) LIKE ?' ,[$search_query]);
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
                    'private' => $user->profile?->private ?? false,
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
