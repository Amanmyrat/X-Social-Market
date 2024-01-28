<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserProfileService
{
    /**
     * @param Request $request
     */
    public static function update(Request $request): void
    {
        $validated = $request->validate([
            'full_name' => ['filled', 'string', 'min:2'],
            'bio' => ['filled', 'string', 'min:3'],
            'location' => ['filled', 'string', 'min:3'],
            'website' => ['filled', 'string', 'min:3'],
            'birthdate' => ['filled', 'date_format:Y-m-d'],
            'gender' => ['filled', 'in:male,female'],
            'payment_available' => ['filled', 'boolean'],
            'private' => ['filled', 'boolean'],
            'profile_image' => ['filled', 'image'],
        ]);

        if(isset($validated['profile_image'])){
            $profileImageName = $request->user()->phone.'-'.time().'.'.$request->profile_image->getClientOriginalExtension();
            $validated['profile_image']->move(public_path('uploads/user/profile'), $profileImageName);
            $validated['profile_image'] = $profileImageName;
        }
        if($request->user()->profile){
            $request->user()->profile()->update($validated);
        }else{
            UserProfile::create(array_merge($validated, ['user_id' => $request->user()->id]));
        }
    }
}
