<?php

namespace App\Transformers;

use App\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    public function transform(Message $message): array
    {
        return [
            'id' => $message->id,
            'chat_id' => $message->chat_id,
            'sender_user_id' => $message->sender_user_id,
            'receiver_user_id' => $message->receiver_user_id,
            'type' => $message->type,
            'body' => $message->body,
            'extra' => $message->extra,
            'isRead' => $message->read_at != null,
            'created_at' => $message->created_at,
        ];
    }
}
