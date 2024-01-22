<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SendMessageRequest;
use App\Jobs\ProcessMessageSent;
use App\Services\MessageService;
use App\Transformers\MessageTransformer;
use Illuminate\Http\JsonResponse;

class MessageController extends ApiBaseController
{
    protected MessageService $messageService;

    /**
     * @param MessageService $messageService
     */
    public function __construct(MessageService $messageService, )
    {
        $this->messageService = $messageService;
        parent::__construct();
    }

    /**
     * Send message
     *
     * @param SendMessageRequest $request
     * @return JsonResponse
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $message = $this->messageService->sendMessage($request->validated());

        ProcessMessageSent::dispatch($message);

        return response()->json(['data' => $message], 201);
    }

    /**
     * Get all messages
     *
     * @param $chatId
     * @return JsonResponse
     */
    public function listMessages($chatId): JsonResponse
    {
        $messages = $this->messageService->listMessages($chatId);
        return $this->respondWithCollection($messages, new MessageTransformer());
    }

    /**
     * Mark message read
     *
     * @param $messageId
     * @return JsonResponse
     */
    public function readMessage($messageId): JsonResponse
    {
        $message = $this->messageService->readMessage($messageId);

        if (!$message) {
            return response()->json(['message' => 'Message not found or access denied'], 404);
        }

        return response()->json(['message' => 'Message marked as read']);
    }

    /**
     * @return JsonResponse
     */
    public function readAllUnreadMessages(): JsonResponse
    {
        // Attempt to mark all unread messages as read
        $unreadMessages = $this->messageService->readAllMessages();

        // Check if there were any unread messages to mark as read
        if ($unreadMessages->isEmpty()) {
            return response()->json(['message' => 'No unread messages found'], 404);
        }

        return response()->json([
            'message' => 'All unread messages marked as read',
            'count' => $unreadMessages->count()
        ]);
    }


}
