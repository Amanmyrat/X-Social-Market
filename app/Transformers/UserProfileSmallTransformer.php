<?php

namespace App\Transformers;

use App\Models\UserProfile;
use League\Fractal\TransformerAbstract;

class UserProfileSmallTransformer extends TransformerAbstract
{
    public function transform(UserProfile $profile): array
    {
        return [
            'user_id' => $profile->user_id,
            'full_name' => $profile->full_name,
            'profile_image' => $profile->profile_image ? url('uploads/user/profile/'.$profile->profile_image) : null,
            'verified' => $profile->verified,
            'private' => $profile->private,
        ];
    }
}
