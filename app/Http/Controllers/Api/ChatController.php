<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateChatRequest;
use App\Services\ChatService;
use App\Transformers\ChatTransformer;
use Illuminate\Http\JsonResponse;

class ChatController extends ApiBaseController
{
    public function __construct(protected ChatService $chatService)
    {
        parent::__construct();
    }

    /**
     * Create chat
     */
    public function createChat(CreateChatRequest $request): JsonResponse
    {
        $receiverUserId = $request->input('receiver_user_id');
        $chat = $this->chatService->findOrCreateChat($receiverUserId);

        return $this->respondWithItem($chat, new ChatTransformer());
    }

    /**
     * List chat
     */
    public function listChats(): JsonResponse
    {
        $chats = $this->chatService->listUserChats();

        return $this->respondWithCollection($chats, new ChatTransformer());
    }
}
