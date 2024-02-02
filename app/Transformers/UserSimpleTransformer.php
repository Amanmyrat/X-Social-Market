<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserSimpleTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->profile?->full_name,
            'profile_image' => $user->profile?->profile_image,
            'last_activity' => $user->last_activity,
        ];
    }
}
