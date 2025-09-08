<?php

namespace App\Transformers;

use App\Models\User;
use Auth;
use League\Fractal\TransformerAbstract;

class UserSimpleTransformer extends TransformerAbstract
{
    public function __construct(protected bool $isFollowingEnabled = false)
    {
    }

    public function transform(User $user): array
    {
        $result = [
            'id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->profile?->full_name,
            'image' => $user->profile?->image_urls,
            'last_activity' => $user->last_activity,
        ];

        if ($this->isFollowingEnabled) {
            $result['private'] = $user->profile?->private ?? false;
            $result['isFollowing'] = Auth::user()->followings()->where('users.id', $user->id)->exists();
        }

        return $result;
    }
}
