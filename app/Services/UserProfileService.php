<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;

class UserProfileService
{
    /**
     * @param array $validated
     * @param User $user
     */
    public static function update(array $validated, User $user): void
    {
        if(isset($validated['profile_image'])){
            $profileImageName = $user->phone.'-'.time().'.'.$validated['profile_image']->getClientOriginalExtension();
            $validated['profile_image']->move(public_path('uploads/user/profile'), $profileImageName);
            $validated['profile_image'] = $profileImageName;
        }
        if($user->profile){
            $user->profile()->update($validated);
        }else{
            UserProfile::create(array_merge($validated, ['user_id' => $user->id]));
        }
    }
}
