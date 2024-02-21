<?php

namespace App\Services;

use App\Models\Chat;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    /**
     * @param int $receiverUserId
     * @param int|null $postId
     * @return Chat
     */
    public function findOrCreateChat(int $receiverUserId, int $postId = null): Chat
    {
        $existingChat = $this->findExistingChat($receiverUserId);

        if ($existingChat) {
            return $existingChat;
        }

        return $this->createNewChat($receiverUserId, $postId);
    }

    /**
     * @param $receiverUserId
     * @return Chat|null
     */
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

    /**
     * @param int $receiverUserId
     * @param int|null $postId
     * @return Chat
     */
    private function createNewChat(int $receiverUserId, int $postId = null): Chat
    {
        return Chat::create([
            'sender_user_id' => auth()->id(),
            'receiver_user_id' => $receiverUserId,
            'post_id' => $postId,
        ]);
    }

    public function listUserChats(): Collection
    {
        $user = Auth::user();

        return $user
            ->chats()
            ->with('post.media')
            ->latest('updated_at')
            ->get(['id', 'sender_user_id', 'receiver_user_id', 'post_id']);
    }
}
