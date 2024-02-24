<?php

namespace App\Services;

use App\Models\User;

class FollowerService
{
    public function follow($following_user_id, User $user): void
    {
        $following = User::find($following_user_id);

        $user->followings()->syncWithoutDetaching($following);
    }

    public function unfollow($following_id, User $user): void
    {
        $following = User::find($following_id);
        $user->followings()->detach($following);
    }

    public function followRequest($following_user_id, User $user): void
    {
        $following = User::find($following_user_id);

        $user->outgoingRequests()->syncWithoutDetaching($following);
    }
}
