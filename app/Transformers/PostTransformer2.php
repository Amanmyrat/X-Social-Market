<?php

namespace App\Transformers;

use App\DTOs\UserPostInteractionsDTO;
use App\Models\Post;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class PostTransformer2 extends TransformerAbstract
{
    public function __construct(
        protected UserPostInteractionsDTO $userInteractions,
        protected array $followings,
        protected array $storyViewUsers
    ) {
    }

    protected array $defaultIncludes = [
        'user',
    ];

    public function transform(Post $post): array
    {
        return [
            'id' => $post->id,
            'caption' => $post->caption,
            'price' => $post->price,
            'description' => $post->description,
            'location' => $post->location,
            'can_comment' => $post->can_comment,
            'created_at' => $post->created_at,
            'rating' => $post->ratings_avg_rating,
            'media' => $post->image_urls,
            'isFavorite' => in_array($post->id, $this->userInteractions->favoritePostIds),
            'isBookmark' => in_array($post->id, $this->userInteractions->bookmarkedPostIds),
            'isViewed' => in_array($post->id, $this->userInteractions->viewedPostIds),
            'is_following' => in_array($post->user->id, $this->followings),
            'private' => $post->user->profile?->private ?? false,
            'has_unviewed_story' => in_array($post->user->id, $this->storyViewUsers),
            'score' => $post->score ?? '',
        ];
    }

    public function includeUser(Post $post): Item
    {
        return $this->item($post->user, new UserSimpleTransformer());
    }
}
