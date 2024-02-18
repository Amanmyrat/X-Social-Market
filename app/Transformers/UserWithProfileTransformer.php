<?php

namespace App\Transformers;

use App\Models\User;
use Auth;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserWithProfileTransformer extends TransformerAbstract
{
    public function __construct(protected bool $isFollowingEnabled = false)
    {}

    protected array $defaultIncludes = [
        'profile',
    ];

    public function transform(User $user): array
    {
        $result = [
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
        if($this->isFollowingEnabled){
            $result['isFollowing'] = Auth::user()->followings()->where('id', $user->id)->exists();
        }
        return $result;
    }

    public function includeProfile(User $user): ?Item
    {
        if ($user->profile) {
            return $this->item($user->profile->load(['location', 'category']), new UserProfileTransformer());
        }

        return null;
    }
}
