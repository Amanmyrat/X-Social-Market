<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorMessage;
use App\Http\Requests\MessageSendRequest;
use App\Jobs\ProcessMessageSent;
use App\Jobs\SendFirebaseMessageNotificationJob;
use App\Models\Chat;
use App\Models\Message;
use App\Services\MessageService;
use App\Transformers\MessageTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

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
     *
     * @throws Throwable
     */
    public function sendMessage(MessageSendRequest $request): JsonResponse
    {
        try {
            $message = $this->messageService->sendMessage($request->validated());

            ProcessMessageSent::dispatch($message);

            if ($message->receiver->device_token) {
                SendFirebaseMessageNotificationJob::dispatch($message, $message->receiver->device_token);
            }

            return $this->respondWithItem($message, new MessageTransformer());
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get all messages
     */
    public function listMessages(Chat $chat): JsonResponse
    {
        $messages = $this->messageService->listMessages($chat);

        return $this->respondWithPaginator($messages, new MessageTransformer());
    }

    /**
     * Mark message read
     */
    public function readMessage(Message $message): JsonResponse
    {
        abort_if(
            Auth::id() != $message->receiver_user_id,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );

        $this->messageService->readMessage($message);

        return response()->json(['message' => 'Message marked as read']);
    }

    /**
     * Mark all message read of the chat
     */
    public function readAllUnreadMessages(Chat $chat): JsonResponse
    {
        abort_if(
            Auth::id() != $chat->sender_user_id
            && Auth::id() != $chat->receiver_user_id,
            403,
            ErrorMessage::UNAUTHORIZED_ACCESS_ERROR->value
        );
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
            $message->sender_user_id != $userId
            && $message->receiver_user_id != $userId,
            403
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
            'Forbidden'
        );

        abort_if(
            $message->type != Message::TYPE_MEDIA,
            403,
            'Message type is not media'
        );

        $medias = $message->extra['medias'];
        $foundMedia = current(array_filter($medias, fn ($item) => $item['id'] === $media->id));

        abort_if(
            ! $foundMedia,
            400,
            'Media not found'
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
