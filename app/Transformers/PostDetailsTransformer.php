<?php

namespace App\Transformers;

use App\DTOs\UserPostInteractionsDTO;
use App\Models\Post;
use Auth;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class PostDetailsTransformer extends TransformerAbstract
{
    public function __construct(
        protected UserPostInteractionsDTO $userInteractions,
        protected array                   $followings)
    {
    }

    protected array $defaultIncludes = [
        'user', 'product', 'tags'
    ];

    public function transform(Post $post): array
    {
        return [
            'id' => $post->id,
            'type' => $post->type,
            'category_id' => $post->category_id,
            'caption' => $post->caption,
            'price' => $post->price,
            'description' => $post->description,
            'location' => $post->location,
            'can_comment' => $post->can_comment,
            'created_at' => $post->created_at,
            'rating' => $post->ratings_avg_rating,
            'media' => $post->image_urls,
            'is_active' => $post->is_active,
            'isFavorite' => in_array($post->id, $this->userInteractions->favoritePostIds),
            'isBookmark' => in_array($post->id, $this->userInteractions->bookmarkedPostIds),
            'isViewed' => in_array($post->id, $this->userInteractions->viewedPostIds),
            'favorites_count' => $post->favorites_count,
            'comments_count' => $post->comments_count,
            'active_comments_count' => $post->active_comments_count,
            'views_count' => $post->views_count,
            'is_following' => in_array($post->user->id, $this->followings),
            'private' => $post->user->profile?->private ?? false,
            'chat' => $post->chats()
                ->where('sender_user_id', Auth::id())
                ->orWhere('receiver_user_id', Auth::id())->first(['id']),
        ];
    }

    public function includeUser(Post $post): Item
    {
        return $this->item($post->user, new UserSimpleTransformer());
    }

    public function includeProduct(Post $post): ?Item
    {
        if ($post->product()->exists()) {
            return $this->item($post->product, new ProductTransformer());
        }

        return null;
    }

    public function includeTags(Post $post): Collection
    {
        return $this->collection($post->tags, new PostTagTransformer());
    }

}
