<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SendMessageRequest;
use App\Jobs\ProcessMessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Services\MessageService;
use App\Transformers\MessageTransformer;
use Illuminate\Http\JsonResponse;

class MessageController extends ApiBaseController
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        parent::__construct();
    }

    /**
     * Send message
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $message = $this->messageService->sendMessage($request->validated());

        ProcessMessageSent::dispatch($message);

        return response()->json(['data' => $message], 201);
    }

    /**
     * Get all messages
     */
    public function listMessages($chatId): JsonResponse
    {
        $messages = $this->messageService->listMessages($chatId);

        return $this->respondWithPaginator($messages, new MessageTransformer());
    }

    /**
     * Mark message read
     */
    public function readMessage(Message $message): JsonResponse
    {
        $this->messageService->readMessage($message);

        return response()->json(['message' => 'Message marked as read']);
    }

    /**
     * Mark all message read of the chat
     */
    public function readAllUnreadMessages(Chat $chat): JsonResponse
    {
        $this->messageService->readAllMessages($chat);

        return response()->json([
            'message' => 'All unread messages marked as read',
        ]);
    }
}
