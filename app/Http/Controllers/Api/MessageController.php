<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SendMessageRequest;
use App\Jobs\ProcessMessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Services\MessageService;
use App\Transformers\MessageTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    /**
     * Delete message
     */
    public function delete(Message $message): JsonResponse
    {
        $userId = Auth::id();
        abort_if(
            $message->sender_user_id != $userId && $message->receiver_user_id != $userId,
            403,
            "Forbidden"
        );

        $message->delete();
        return $this->respondWithMessage('Successfully deleted message');
    }

    /**
     * Delete message image
     */
    public function deleteImage(Message $message, Media $media): JsonResponse
    {
        $userId = Auth::id();
        abort_if(
            $message->sender_user_id != $userId && $message->receiver_user_id != $userId,
            403,
            "Forbidden"
        );

        abort_if(
            $message->type != Message::TYPE_MEDIA,
            403,
            "Message type is not media"
        );

        $medias = $message->extra['medias'];
        $foundMedia = current(array_filter($medias, fn($item) => $item['id'] === $media->id));

        abort_if(
            !$foundMedia,
            400,
            "Media not found"
        );
        $newMediaExtras = array_filter($medias, function ($media) use ($foundMedia) {
            return $media['id'] !== $foundMedia['id'];
        });
        $filteredMedias['medias'] = array_values($newMediaExtras);

        $media->delete();

        $message->extra = $filteredMedias;
        $message->save();

        return $this->respondWithMessage('Successfully deleted image');
    }
}
