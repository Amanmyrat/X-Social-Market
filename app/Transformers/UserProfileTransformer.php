<?php

namespace App\Transformers;

use App\Models\UserProfile;
use League\Fractal\TransformerAbstract;

class UserProfileTransformer extends TransformerAbstract
{

    public function transform(UserProfile $profile): array
    {
        return [
            'user_id' => $profile->user_id,
            'full_name' => $profile->full_name,
            'profile_image' => url('/images/profile/'.$profile->profile_image),
            'bio' => $profile->bio,
            'location' => $profile->location,
            'website' => $profile->website,
            'birthdate' => $profile->birthdate,
            'gender' => $profile->gender,
            'payment_available' => $profile->payment_available,
            'verified' => $profile->verified,
            'private = $profile' => $profile->private
        ];
    }

}
