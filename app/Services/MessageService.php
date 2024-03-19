<?php

namespace App\Services;

use App\Enum\ErrorMessage;
use App\Jobs\ProcessMessageRead;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Post;
use App\Models\Story;
use DB;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Throwable;

class MessageService
{
    /**
     * Send message
     * @throws Exception
     * @throws Throwable
     */
    public function sendMessage(array $data): Message
    {
        return DB::transaction(function () use ($data) {
            $extras = $this->handleExtrasBasedOnType($data);

            $messageData = [
                'chat_id' => (int)$data['chat_id'],
                'sender_user_id' => auth()->id(),
                'receiver_user_id' => (int)$data['receiver_user_id'],
                'body' => $data['body'],
                'type' => $data['type'],
                'extra' => $extras,
            ];

            $message = Message::create($messageData);

            if ($data['type'] === Message::TYPE_MEDIA) {
                $this->handleMediaMessage($message, $data);
            }

            $message->chat->updated_at = now();
            $message->chat->save();

            return $message;
        }, 2);
    }

    private function handleExtrasBasedOnType(array $data): ?array
    {
        $extras = null;
        $type = $data['type'];

        if ($type === Message::TYPE_SHARE_STORY) {
            $extras['story'] = $this->getStoryDetails($data['story_id']);
        }

        if ($type === Message::TYPE_SHARE_POST) {
            $extras['post'] = $this->getPostDetails($data['post_id']);
        }

        return $extras;
    }

    private
    function getStoryDetails($storyId): Story
    {
        /** @var Story $story */
        $story = Story::with('user:id,username,last_activity')
            ->where('id', $storyId)
            ->first(['id', 'user_id', 'image', 'post_id']);

        if ($story) {
            $story->image = $story->image_urls;
        }

        return $story;
    }

    private
    function getPostDetails($postId): Post
    {
        /** @var Post $post */
        $post = Post::with(['user:id,username,last_activity', 'media'])
            ->where('id', $postId)
            ->first(['id', 'user_id', 'caption']);

        if ($post) {
            $medias = $post->image_urls;
            unset($post->media);
            $post->media = $medias;

        }

        return $post;
    }

    private function handleMediaMessage(Message $message, array $data): void
    {
        // Determine the type of media (images or videos)
        $mediaType = $data['media_type'] == 'image' ? 'images' : 'videos';

        $message->addMultipleMediaFromRequest([$mediaType])
            ->each(function ($file) {
                $file->toMediaCollection('message_medias');
            });
    }

    /**
     * Get all messages
     */
    public
    function listMessages($chatId): LengthAwarePaginator
    {
        $userId = auth()->id();

        return Message::where('chat_id', $chatId)
            ->where(function ($query) use ($userId) {
                $query->where('sender_user_id', $userId)
                    ->whereNull('sender_deleted_at');
            })->orWhere(function ($query) use ($userId) {
                $query->where('receiver_user_id', $userId)
                    ->whereNull('receiver_deleted_at');
            })->latest()->paginate();
    }

    /**
     * Mark message read
     */
    public
    function readMessage(Message $message): void
    {
        $message->update(['read_at' => now()]);

        ProcessMessageRead::dispatch($message);
    }

    public
    function readAllMessages(Chat $chat): void
    {
        $userId = auth()->id();

        Message::where('chat_id', $chat->id)
            ->where('receiver_user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

    }
}
