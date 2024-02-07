<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    /**
     * @param $receiverUserId
     * @return Chat
     */
    public function findOrCreateChat($receiverUserId): Chat
    {
        $existingChat = $this->findExistingChat($receiverUserId);

        if ($existingChat) {
            return $existingChat;
        }

        return $this->createNewChat($receiverUserId);
    }

    /**
     * @param $receiverUserId
     * @return Chat|null
     */
    private function findExistingChat($receiverUserId): Chat|null
    {
        return Chat::where(function ($query) use ($receiverUserId) {
            $query->where('sender_user_id', auth()->id())
                ->where('receiver_user_id', $receiverUserId);
        })->orWhere(function ($query) use ($receiverUserId) {
            $query->where('sender_user_id', $receiverUserId)
                ->where('receiver_user_id', auth()->id());
        })->first();
    }

    /**
     * @param $receiverUserId
     * @return Chat
     */
    private function createNewChat($receiverUserId): Chat
    {
        return Chat::create([
            'sender_user_id' => auth()->id(),
            'receiver_user_id' => (int) $receiverUserId,
        ]);
    }

    /**
     * @return Collection
     */
    public function listUserChats(): Collection
    {
        /** @var User $user */
        $user = auth('sanctum')->user();

        return $user
            ->chats()
            ->latest('updated_at')
            ->get(['id', 'sender_user_id', 'receiver_user_id']);
    }
}
