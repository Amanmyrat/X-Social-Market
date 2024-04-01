<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use DB;
use Throwable;

class UserProfileService
{
    /**
     * @throws Throwable
     */
    public function update(User $user, array $validated): void
    {
        $user->update($validated);
        if (isset($validated['profile'])) {
            DB::transaction(function () use ($validated, $user) {

                if (isset($validated['profile']['profile_image'])) {
                    $profileImage = $validated['profile']['profile_image'];
                }
                unset($validated['profile']['profile_image']);

                if ($user->profile) {
                    $user->profile()->update($validated['profile']);
                } else {
                    $validated['profile'] = count($validated['profile']) > 0 ? $validated['profile'] : ['bio' => ''];

                    UserProfile::create(array_merge($validated['profile'], ['user_id' => $user->id]));
                }

                $user->load('profile');

                if (isset($profileImage)) {
                    if ($user->profile->hasMedia()) {
                        $user->profile->clearMediaCollection('user_images');
                    }
                    $user->profile->addMedia($profileImage)->toMediaCollection('user_images');
                }
            });
        }

    }
}
