<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserProfile;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserWithProfileTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'profile'
    ];

    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'phone' => $user->phone,
            'username' => $user->username,
            'email' => $user->email ?? '',
            'type' => $user->type,
            'last_activity' => $user->last_activity,
            'is_active' => $user->is_active,
            'blocked_at' => $user->blocked_at,
            'block_reason' => $user->block_reason,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'posts_count' => $user->posts_count,
            'followers_count' => $user->followers_count,
            'followings_count' => $user->followings_count,
        ];
    }

    public function includeProfile(User $user): ?Item
    {
        if($user->profile){
            return $this->item($user->profile->load(['location', 'category']), new UserProfileTransformer());
        }
        return null;
    }

}
