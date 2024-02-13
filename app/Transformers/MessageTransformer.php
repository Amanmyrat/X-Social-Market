<?php

namespace App\Transformers;

use App\Models\Message;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    #[ArrayShape(['id' => 'int', 'sender_user_id' => 'int', 'receiver_user_id' => 'int', 'type' => 'string', 'body' => 'string', 'extra' => 'array', 'isRead' => 'bool', 'created_at' => 'string'])]
    public function transform(Message $message): array
    {
        return [
            'id' => $message->id,
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
