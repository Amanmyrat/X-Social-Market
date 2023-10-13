<?php

namespace App\Services;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostService
{
    public static function create(PostRequest $request): void
    {
        $post = Post::create(array_merge($request->validated(), [
            'user_id' => $request->user()->id
        ]));

        $medias = $request->validated()['media_type'] == 'image'
            ? 'images'
            : 'videos';

        $post->addMultipleMediaFromRequest([$medias])
            ->each(function ($fileAdder) {

                $fileAdder->toMediaCollection();
            });

    }

    public static function searchPosts(Request $request)
    {
        $limit = $request->get('limit');

        $products = Post::when(isset($request->categories), function ($query) use ($request) {
                return $query->whereIn('category_id', $request->categories);
            })
            ->when(isset($request->price_min), function ($query) use ($request) {
                return $query->where('price', '>=', $request->price_min);
            })
            ->when(isset($request->price_max), function ($query) use ($request) {
                return $query->where('price', '<=', $request->price_max);
            })
            ->when(isset($request->date_start), function ($query) use ($request) {
                return $query->where('created_at', '>=', $request->date_start);
            })
            ->when(isset($request->date_end), function ($query) use ($request) {
                return $query->where('created_at', '<=', $request->date_end);
            })
            ->when(isset($request->search_query), function ($query) use ($request) {
                $search_query = '%' . $request->search_query . '%';
                return $query->where('caption', 'LIKE', $search_query)
                    ->orWhere('description', 'LIKE', $search_query);
            });

        if ($s = $request->get('sort')) {
            switch ($s) {
                case 'most_liked':
                    $products = $products
                        ->withCount('favorites')
                        ->orderByDesc('favorites_count');
                    break;
                default:
                    $sort = self::getSort($s);
                    $products = $products->orderBy('posts.' . $sort[0], $sort[1]);
            }
        } else {
            $products = $products->inRandomOrder();
        }
        return $products->limit($limit)->get();
    }

    private static function getSort($sort): array
    {
        $sort_key = trim($sort, '-');

        if (str_contains($sort, '-') && strpos($sort, '-') == 0) {
            $sort_direction = 'desc';
        }
        return [
            $sort_key,
            $sort_direction ?? 'asc'
        ];
    }
}
