<?php

namespace App\Services;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Product;
use DB;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Throwable;

class PostService
{
    /**
     * @param array $postData
     * @param int $userId
     * @param array $productData
     * @return bool
     * @throws Throwable
     */
    public function create(array $postData, int $userId, array $productData = []): bool
    {
        try {
            $exception = DB::transaction(function () use ($postData, $productData, $userId) {
                $post = Post::create($postData + [
                        'user_id' => $userId,
                    ]);

                $medias = $postData['media_type'] == 'image'
                    ? 'images'
                    : 'videos';

                $post->addMultipleMediaFromRequest([$medias])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });

                if ($post->category->has_product) {
                    $product = new Product($productData);
                    $product->post()->associate($post);
                    $product->save();
                }

            });

            return is_null($exception) ? true : $exception;

        } catch (Exception $e) {
            return false;
        }

    }

    public function searchPosts(Request $request): LengthAwarePaginator
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
                    $sort = $this->getSort($s);
                    $products = $products->orderBy('posts.' . $sort[0], $sort[1]);
            }
        } else {
            $products = $products->inRandomOrder();
        }

        return $products->withCount(['favorites', 'comments', 'views'])->paginate($limit);
    }

    private function getSort($sort): array
    {
        $sort_key = trim($sort, '-');

        if (str_contains($sort, '-') && strpos($sort, '-') == 0) {
            $sort_direction = 'desc';
        }

        return [
            $sort_key,
            $sort_direction ?? 'asc',
        ];
    }
}
