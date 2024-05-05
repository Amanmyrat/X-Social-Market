<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function findOrCreateChat(int $receiverUserId, int $userId, ?int $postId = null): Chat
    {
        $existingChat = $this->findExistingChat($receiverUserId, $postId);

        if ($existingChat) {
            return $existingChat;
        }

        return Chat::create([
            'sender_user_id' => $userId,
            'receiver_user_id' => $receiverUserId,
            'post_id' => $postId,
        ]);
    }

    private function findExistingChat($receiverUserId, $postId): ?Chat
    {
        return Chat::when(isset($postId), function ($query) use ($postId) {
            return $query->where('post_id', $postId);
        })->where(function ($query) use ($receiverUserId) {
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
            ->with(['post.media', 'latestMessage',
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
