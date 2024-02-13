<?php

namespace App\Services;

use App\Jobs\ProcessMessageRead;
use App\Models\Message;
use App\Models\Post;
use App\Models\Story;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MessageService
{
    /**
     * Send message
     */
    public function sendMessage(array $data): Message
    {
        $extras = $this->handleExtrasBasedOnType($data);

        $messageData = [
            'chat_id' => (int) $data['chat_id'],
            'sender_user_id' => auth()->id(),
            'receiver_user_id' => (int) $data['receiver_user_id'],
            'body' => $data['body'],
            'type' => $data['type'],
            'extra' => $extras,
        ];

        $message = Message::create($messageData);

        if ($data['type'] === Message::TYPE_MEDIA) {
            $message = $this->handleMediaMessage($message, $data);
        }

        $message->chat->updated_at = now();
        $message->chat->save();

        return $message;
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

    private function getStoryDetails($storyId): Builder|Story
    {
        /** @var Story $story */
        $story = Story::with('user:id,username,last_activity')
            ->where('id', $storyId)
            ->first(['id', 'user_id', 'image', 'post_id']);

        if ($story) {
            $story->image = $story->image ? url('uploads/stories/'.$story->image) : null;
        }

        return $story;
    }

    private function getPostDetails($postId): Builder|Post
    {
        $post = Post::with('user:id,username,last_activity')
            ->where('id', $postId)
            ->first(['id', 'user_id', 'caption']);

        if ($post) {
            $medias = $post->getMedia()->map(function ($media) {
                return [
                    'original_url' => $media->original_url,
                    'extension' => $media->extension,
                    'size' => $media->size,
                ];
            });
            unset($post->media);
            $post->media = $medias;

        }

        return $post;
    }

    private function handleMediaMessage(Message $message, array $data): Message
    {
        // Determine the type of media (images or videos)
        $mediaType = $data['media_type'] == 'image' ? 'images' : 'videos';

        $message->addMultipleMediaFromRequest([$mediaType])
            ->each(function ($file) {
                $file->toMediaCollection();
            });

        $medias = $message->getMedia()->map(function ($media) {
            return [
                'original_url' => $media->original_url,
                'extension' => $media->extension,
                'size' => $media->size,
            ];
        });

        $extras = $message->extra ?? [];
        $extras['medias'] = $medias;
        $message->extra = $extras;

        unset($message->media);

        $message->save();

        return $message;
    }

    /**
     * Get all messages
     */
    public function listMessages($chatId): LengthAwarePaginator
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
    public function readMessage($messageId): ?Message
    {
        $message = Message::find($messageId);
        if (! $message || $message->receiver_user_id !== auth()->id()) {
            return null;
        }

        $message->read_at = now();
        $message->save();

        ProcessMessageRead::dispatch($message);

        return $message;
    }

    public function readAllMessages(): Collection
    {
        $userId = auth()->id();

        $unreadMessages = Message::where('receiver_user_id', $userId)
            ->whereNull('read_at')
            ->get();

        if ($unreadMessages->isEmpty()) {
            return collect();
        }

        foreach ($unreadMessages as $message) {
            $message->read_at = now();
            $message->save();

            ProcessMessageRead::dispatch($message);
        }

        return $unreadMessages;
    }
}
