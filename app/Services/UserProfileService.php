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
    public function update(array $validated, User $user): void
    {
        $user->update($validated);
        DB::transaction(function () use ($validated, $user) {

            if ($user->profile) {
                $user->profile()->update($validated['profile']);
            } else {
                UserProfile::create(array_merge($validated['profile'], ['user_id' => $user->id]));
            }

            if (isset($validated['profile']['profile_image'])) {
                $user->profile->clearMediaCollection('user_images');
                $user->profile->addMedia($validated['profile']['profile_image'])->toMediaCollection('user_images');
            }
        });
    }
}
