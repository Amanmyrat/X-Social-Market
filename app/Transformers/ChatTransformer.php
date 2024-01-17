<?php

namespace App\Transformers;

use App\Models\Chat;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ChatTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'receiver', 'last_message'
    ];

    #[ArrayShape(['id' => "int", 'sender_user_id' => "int", 'receiver_user_id' => "int", 'unread_messages' => "int"])]
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
        return $this->item($chat->getReceiver(), new UserTransformer());
    }

    public function includeLastMessage(Chat $chat): Item|null
    {
        if ($chat->messages()->count()) {
            return $this->item($chat->messages()->latest()->first(), new MessageTransformer());
        }
        return null;
    }
}
