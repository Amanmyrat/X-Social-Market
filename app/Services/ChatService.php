<?php

namespace App\Services;

use App\Models\Chat;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function findOrCreateChat($receiverUserId): Chat
    {
        $existingChat = $this->findExistingChat($receiverUserId);

        if ($existingChat) {
            return $existingChat;
        }

        return $this->createNewChat($receiverUserId);
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

    private function createNewChat($receiverUserId): Chat
    {
        return Chat::create([
            'sender_user_id' => auth()->id(),
            'receiver_user_id' => (int) $receiverUserId,
        ]);
    }

    public function listUserChats(): Collection
    {
        $user = Auth::user();

        return $user
            ->chats()
            ->latest('updated_at')
            ->get(['id', 'sender_user_id', 'receiver_user_id']);
    }
}
