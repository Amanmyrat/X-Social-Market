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
     * @param $chatId
     * @return JsonResponse
     */
    public function listMessages($chatId): JsonResponse
    {
        $messages = $this->messageService->listMessages($chatId);
        return $this->respondWithCollection($messages, new MessageTransformer());
    }

    /**
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
}
