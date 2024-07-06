<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function findOrCreateChat(int $receiverUserId, int $userId): Chat
    {
        $existingChat = $this->findExistingChat($receiverUserId);

        if ($existingChat) {
            return $existingChat;
        }

        return Chat::create([
            'sender_user_id' => $userId,
            'receiver_user_id' => $receiverUserId,
        ]);
    }

    private function findExistingChat($receiverUserId): ?Chat
    {
        return Chat::where(function ($query) use ($receiverUserId) {
            $query->where('sender_user_id', auth()->id())
                ->where('receiver_user_id', $receiverUserId);
        })->orWhere(function ($query) use ($receiverUserId) {
            $query->where('sender_user_id', $receiverUserId)
                ->where('receiver_user_id', auth()->id());
        })->first();
    }

    public function listUserChats(User $user): Collection
    {
        return $user
            ->chats()
            ->with(['latestMessage',
                'receiver.profile.media',
                'sender.profile.media',
            ])
            ->withCount(['messages' => function ($query) use ($user) {
                $query->where('receiver_user_id', $user->id)
                    ->whereNull('read_at');
            }])
            ->latest('updated_at')
            ->get();
    }
}
