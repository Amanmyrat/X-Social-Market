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
            'image' => $profile->image_urls,
            'verified' => $profile->verified,
            'private' => $profile->private,
        ];
    }
}
