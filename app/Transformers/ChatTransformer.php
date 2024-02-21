<?php

namespace App\Transformers;

use App\Models\Chat;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ChatTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'receiver', 'last_message', 'product',
    ];

    public function transform(Chat $chat): array
    {
        return [
            'id' => $chat->id,
            'sender_user_id' => $chat->sender_user_id,
            'receiver_user_id' => $chat->receiver_user_id,
            'unread_messages' => $chat->unreadMessagesCount(),
        ];
    }

    public function includeReceiver(Chat $chat): Item
    {
        return $this->item($chat->getReceiver(), new UserSimpleTransformer());
    }

    public function includeProduct(Chat $chat): ?Item
    {
        if ($chat->post->exists) {
            return $this->item($chat->post, new PostSimpleTransformer());
        }

        return null;
    }

    public function includeLastMessage(Chat $chat): ?Item
    {
        if ($chat->messages()->count()) {
            return $this->item($chat->messages()->latest()->first(), new MessageTransformer());
        }

        return null;
    }
}
