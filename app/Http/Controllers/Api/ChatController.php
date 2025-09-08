<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorMessage;
use App\Http\Requests\ChatCreateRequest;
use App\Models\Chat;
use App\Models\User;
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
    public function createChat(ChatCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $receiverUserId = $validated['receiver_user_id'];

        $receiverUser = User::find($receiverUserId);

        abort_if(
            Auth::user()->type == 'user' && $receiverUser->type == 'user',
            403,
            ErrorMessage::GENERAL_ERROR->value
        );

        // Check if receiver user has blocked current user
        abort_if(
            $receiverUser->blockedUsers->contains(Auth::user()),
            403,
            ErrorMessage::USER_BLOCKED_ERROR->value
        );

        $chat = $this->chatService->findOrCreateChat($receiverUserId, Auth::id());

        return $this->respondWithItem($chat, new ChatTransformer());
    }

    /**
     * List chat
     */
    public function listChats(): JsonResponse
    {
        $chats = $this->chatService->listUserChats(Auth::user());

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
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR
        );

        $chat->delete();

        return $this->respondWithMessage('Successfully deleted');
    }
}
