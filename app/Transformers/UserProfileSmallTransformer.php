<?php

namespace App\Transformers;

use App\Models\UserProfile;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class UserProfileSmallTransformer extends TransformerAbstract
{
    #[ArrayShape(['user_id' => "mixed", 'full_name' => "mixed", 'profile_image' => "mixed", 'verified' => "mixed", 'private' => "mixed"])]
    public function transform(UserProfile $profile): array
    {
        return [
            'user_id' => $profile->user_id,
            'full_name' => $profile->full_name,
            'profile_image' => $profile->profile_image ? url('uploads/user/profile/'.$profile->profile_image) : null,
            'verified' => $profile->verified,
            'private' => $profile->private
        ];
    }

}
