<?php

namespace App\Services;

use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;

class UserBlockService
{
    public static function block(Request $request): void
    {
        $validated = $request->validate([
            'block_user_id' => ['required', 'integer', 'exists:'.User::class.',id', 'not_in:'.auth('sanctum')->user()->id],
        ]);

        $block = User::find($validated['block_user_id']);

        auth('sanctum')->user()->blockedUsers()->syncWithoutDetaching($block);
    }

    public static function unblock(Request $request): void
    {
        $validated = $request->validate(
            [
                'block_user_id' => ['required', 'integer', 'exists:'.User::class.',id', 'exists:'.BlockedUser::class.',blocked_user_id'],
            ]
        );
        $block = User::find($validated['block_user_id']);
        auth('sanctum')->user()->blockedUsers()->detach($block);
    }
}
