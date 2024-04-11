<?php

namespace App\Services;

use App\Models\Follower;
use App\Models\User;

class FollowerService
{
    public function follow(int $following_user_id, User $user): void
    {
        $follower = Follower::where('following_user_id', $user->id)
            ->where('followed_user_id', $following_user_id)
            ->first();

        if ($follower) {
            if ($follower->unfollowed_at !== null) {
                $follower->unfollowed_at = null;
                $follower->save();
            }
        } else {
            Follower::create([
                'following_user_id' => $user->id,
                'followed_user_id' => $following_user_id,
                'unfollowed_at' => null, // This might be omitted if your default is null
            ]);
        }
    }

    public function unfollow($following_user_id, User $user): void
    {
        $follower = Follower::where('following_user_id', $user->id)
            ->where('followed_user_id', $following_user_id)
            ->first();

        if ($follower) {
            $follower->unfollowed_at = now();
            $follower->save();
        }
    }

    public function followRequest($following_user_id, User $user): void
    {
        $following = User::find($following_user_id);

        $user->outgoingRequests()->syncWithoutDetaching($following);
    }
}
