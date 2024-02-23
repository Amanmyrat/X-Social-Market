<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateChatRequest;
use App\Models\Chat;
use App\Services\ChatService;
use App\Transformers\ChatTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        $postId = $request->input('post_id') ?? null;
        $chat = $this->chatService->findOrCreateChat($receiverUserId, $postId);

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

    /**
     * Delete chat
     */
    public function delete(Chat $chat): JsonResponse
    {
        $userId = Auth::id();
        abort_if(
            $chat->sender_user_id != $userId && $chat->receiver_user_id != $userId,
            403,
            "Forbidden"
        );

        $chat->delete();
        return $this->respondWithMessage('Successfully deleted');
    }
}
