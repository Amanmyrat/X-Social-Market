<?php

namespace App\Services;

use App\Jobs\ProcessUserOffline;
use App\Jobs\ProcessUserOnline;
use App\Models\Brand;
use App\Models\Location;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserService
{
    /**
     * @param Request $request
     */
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

    /**
     * @param Request $request
     */
    public static function updatePhone(Request $request): void
    {
        $validated = $request->validate(
            [
                'phone' => ['required', 'integer', 'unique:' . User::class],
            ]
        );
        $request->user()->update($validated);
    }

    /**
     * @param Request $request
     */
    public static function newPassword(Request $request): void
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed',  Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
    }

    /**
     * @param Request $request
     */
    public static function update(Request $request): void
    {
        $validated = $request->validate([
            'username' => ['filled', 'string', 'min:3', 'alpha_dash', 'unique:' . User::class],
            'email' => ['filled', 'email', 'unique:' . User::class],
        ]);
        $request->user()->update($validated);
    }

    /**
     * @param User $user
     * @param bool $isOnline
     */
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
     * Get user list
     *
     * @param string $type
     * @param int $limit
     * @param string|null $search_query
     * @return LengthAwarePaginator
     */
    public function list(string $type, int $limit, string $search_query = null): LengthAwarePaginator
    {
        return User::where('type', $type)->when(isset($search_query), function ($query) use ($search_query) {
            $search_query = '%' . $search_query . '%';
            return $query->whereHas('profile', function($q) use ($search_query){
                $q->where('full_name',$search_query);
            })->where('phone', 'LIKE', $search_query)->orWhere('username', 'LIKE', $search_query);
        })->latest()->paginate($limit);
    }

    /**
     * Update user
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateWithProfile(User $user, array $data): User
    {
        if(isset($data['profile']['profile_image'])){
            $profileImageName = $user->phone.'-'.time().'.'.$data['profile']['profile_image']->getClientOriginalExtension();
            $data['profile']['profile_image']->move(public_path('uploads/user/profile'), $profileImageName);
            $data['profile']['profile_image'] = $profileImageName;
        }
        $user->update($data);

        if($data['profile']){
            $user->profile()->update($data['profile']);
        }

        return $user;
    }
}
