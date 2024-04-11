<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Product;
use Arr;
use Auth;
use DB;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Str;
use Throwable;

class PostService
{
    /**
     * @throws Throwable
     */
    public function create(array $validated, int $userId): Post
    {
        $postData = Arr::except($validated, 'product');
        $productData = Arr::only($validated, 'product')['product'] ?? [];

        return DB::transaction(function () use ($postData, $productData, $userId) {
            $activePostsCount = Post::where('user_id', $userId)->where('is_active', true)->count();
            $isActive = $activePostsCount >= 10;

            $post = Post::create($postData + [
                    'user_id' => $userId,
                    'is_active' => $isActive,
                ]);

            $medias = $postData['media_type'] == 'image'
                ? 'images'
                : 'videos';

            $post->addMultipleMediaFromRequest([$medias])
                ->each(function ($fileAdder) {
                    $fileAdder->toMediaCollection('post_medias');
                });

            if ($post->category->has_product) {
                $product = new Product($productData);

                $uniqueColorIds = collect($productData['options']['colors'] ?? [])
                    ->pluck('color_id')->unique()->values()->all();

                $uniqueSizeIds = collect($productData['options']['colors'] ?? [])
                    ->flatMap(function ($color) {
                        return collect($color['sizes'])->pluck('size_id');
                    })->unique()->values()->all();

                $product->post()->associate($post);
                $product->save();

                $product->colors()->attach($uniqueColorIds);
                $product->sizes()->attach($uniqueSizeIds);
            }

            return $post;
        });

    }

    /**
     * @throws Throwable
     */
    public function update(Post $post, array $validated): Post
    {
        $postData = Arr::except($validated, 'product');
        $productData = Arr::only($validated, 'product')['product'] ?? [];

        return DB::transaction(function () use ($postData, $productData, $post) {
            $medias = $postData['media_type'] == 'image'
                ? 'images'
                : 'videos';

            $post->clearMediaCollection();

            $post->addMultipleMediaFromRequest([$medias])
                ->each(function ($fileAdder) {
                    $fileAdder->toMediaCollection('post_medias');
                });
            if ($post->category->has_product) {
                $product = $post->product;

                $uniqueColorIds = collect($productData['options']['colors'] ?? [])
                    ->pluck('color_id')->unique()->values()->all();

                $uniqueSizeIds = collect($productData['options']['colors'] ?? [])
                    ->flatMap(function ($color) {
                        return collect($color['sizes'])->pluck('size_id');
                    })->unique()->values()->all();

                if ($product) {
                    $product->colors()->sync($uniqueColorIds);
                    $product->sizes()->sync($uniqueSizeIds);
                    $product->update($productData);
                } else {
                    $product = new Product($productData);

                    $product->post()->associate($post);
                    $product->save();

                    $product->colors()->attach($uniqueColorIds);
                    $product->sizes()->attach($uniqueSizeIds);
                }

            } elseif ($post->product()->exists()) {
                $post->product->delete();
            }
            $post->update($postData);

            return $post;
        });
    }

    public function searchPosts(Request $request): LengthAwarePaginator
    {
        $limit = $request->get('limit');

        $posts = Post::activeAndNotBlocked(Auth::id())->with('media')->select(['posts.id', 'posts.caption', 'posts.price', 'posts.media_type'])->when(isset($request->categories), function ($query) use ($request) {
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
                    $posts = $posts
                        ->withCount('favorites')
                        ->orderByDesc('favorites_count');
                    break;
                default:
                    $sort = $this->getSort($s);
                    $posts = $posts->orderBy('posts.' . $sort[0], $sort[1]);
            }
        } else {
            $posts = $posts->inRandomOrder();
        }

        return $posts->paginate($limit);
    }

    public function filter(array $filters): Builder
    {
        $query = Post::query();

        if (isset($filters['price_min'], $filters['price_max'])) {
            $query->whereBetween('posts.price', [$filters['price_min'], $filters['price_max']]);
        }

        if (!empty($filters['brands']) || !empty($filters['colors']) || !empty($filters['sizes'])) {

            $query->whereHas('posts.product', function ($query) use ($filters) {
                if (!empty($filters['brands'])) {
                    $query->whereIn('brand_id', $filters['brands']);
                }

                if (!empty($filters['colors'])) {
                    $query->whereHas('colors', function ($query) use ($filters) {
                        $query->whereIn('colors.id', $filters['colors']);
                    });
                }

                if (!empty($filters['sizes'])) {
                    $query->whereHas('sizes', function ($query) use ($filters) {
                        $query->whereIn('sizes.id', $filters['sizes']);
                    });
                }
            });
        }

        if (!empty($filters['sort'])) {
            $direction = Str::startsWith($filters['sort'], '-') ? 'desc' : 'asc';
            $sortField = ltrim($filters['sort'], '-');

            switch ($sortField) {
                case 'price':
                    $query->orderBy('posts.price', $direction);
                    break;
            }
        }

        return $query;
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
