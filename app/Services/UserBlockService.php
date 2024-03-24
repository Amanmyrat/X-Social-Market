<?php

namespace App\Services;

use App\Models\BlockedUser;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UserBlockService
{
    public function block(array $validated): void
    {
        $block = User::find($validated['block_user_id']);

        Auth::user()->blockedUsers()->syncWithoutDetaching($block);
    }

    public function unblock(array $validated): void
    {
        $block = User::find($validated['block_user_id']);
        Auth::user()->blockedUsers()->detach($block);
    }
}
