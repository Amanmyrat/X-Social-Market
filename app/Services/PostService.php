<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Product;
use Arr;
use Auth;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Str;
use Throwable;

class PostService
{
    /**
     * @throws Throwable
     */
    public function createProduct(array $validated, int $userId): Post
    {
        $postData = Arr::except($validated, 'product');
        $productData = Arr::only($validated, 'product')['product'] ?? [];

        return DB::transaction(function () use ($postData, $productData, $userId) {

            $post = $this->create($postData, $userId, 'product');

            if ($post->category->has_product) {
                $product = new Product($productData);

                // Extract unique color IDs directly from the product data
                $uniqueColorIds = collect($productData['colors'] ?? [])
                    ->unique()->values()->all();

                // Extract unique size IDs directly from the product data
                $uniqueSizeIds = collect($productData['sizes'] ?? [])
                    ->unique()->values()->all();

                $product->post()->associate($post);
                $product->save();

                if (!empty($uniqueColorIds)) {
                    $product->colors()->attach($uniqueColorIds);
                }

                if (!empty($uniqueSizeIds)) {
                    $product->sizes()->attach($uniqueSizeIds);
                }

            }

            return $post;
        });

    }

    /**
     * @throws Throwable
     */
    public function createPost(array $postData, int $userId): Post
    {
        return DB::transaction(function () use ($postData, $userId) {
            return $this->create($postData, $userId, 'post');
        });
    }

    private function create(array $postData, int $userId, string $type): Model|Post
    {
        $activePostsCount = Post::where('user_id', $userId)->where('is_active', true)->count();
        $isActive = $activePostsCount >= 10;

        $post = Post::create($postData + [
                'user_id' => $userId,
                'is_active' => $isActive,
                'type' => $type
            ]);

        $post->addMultipleMediaFromRequest(['medias'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('post_medias');
            });

        return $post;
    }

    /**
     * @throws Throwable
     */
    public function updateProduct(Post $post, array $validated): Post
    {
        $postData = Arr::except($validated, 'product');
        $productData = Arr::only($validated, 'product')['product'] ?? [];

        return DB::transaction(function () use ($postData, $productData, $post) {

            if (isset($postData['medias'])) {
                $post->clearMediaCollection();

                $post->addMultipleMediaFromRequest(['medias'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection('post_medias');
                    });
            }
            if ($post->category->has_product) {
                $product = $post->product;

                // Extract unique color IDs directly from the product data
                $uniqueColorIds = collect($productData['colors'] ?? [])
                    ->unique()->values()->all();

                // Extract unique size IDs directly from the product data
                $uniqueSizeIds = collect($productData['sizes'] ?? [])
                    ->unique()->values()->all();

                if ($product) {
                    $product->colors()->sync($uniqueColorIds);
                    $product->sizes()->sync($uniqueSizeIds);
                    $product->update($productData);
                } else {
                    $product = new Product($productData);

                    $product->post()->associate($post);
                    $product->save();

                    if (!empty($uniqueColorIds)) {
                        $product->colors()->attach($uniqueColorIds);
                    }

                    if (!empty($uniqueSizeIds)) {
                        $product->sizes()->attach($uniqueSizeIds);
                    }
                }

            } elseif ($post->product()->exists()) {
                $post->product->delete();
            }
            $post->update($postData);

            return $post;
        });
    }

    /**
     * @throws Throwable
     */
    public function updatePost(Post $post, array $postData): Post
    {
        return DB::transaction(function () use ($postData, $post) {

            if (isset($postData['medias'])) {
                $post->clearMediaCollection();

                $post->addMultipleMediaFromRequest(['medias'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection('post_medias');
                    });
            }
            $post->update($postData);

            return $post;
        });
    }

    public function searchPosts(Request $request): LengthAwarePaginator
    {
        $limit = $request->get('limit');

        $posts = Post::activeAndNotBlocked(Auth::id())
            ->with('media')
            ->select(['posts.id', 'posts.caption', 'posts.price'])
            ->when(isset($request->categories), function ($query) use ($request) {
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
            ->where(function ($q) use ($request) {
                if (isset($request->search_query)) {
                    $search_query = '%' . strtolower($request->search_query) . '%';
                    $q->where(function ($q) use ($search_query) {
                        $q->whereRaw('LOWER(posts.caption) LIKE ?', [$search_query])
                            ->orWhereRaw('LOWER(posts.description) LIKE ?', [$search_query]);
                    });
                }
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

        if (isset($filters['user_id'])) {
            $query->where('posts.user_id', $filters['user_id']);
        }

        if (isset($filters['price_min'], $filters['price_max'])) {
            $query->whereBetween('posts.price', [$filters['price_min'], $filters['price_max']]);
        }

        if (!empty($filters['brands']) || !empty($filters['colors']) || !empty($filters['sizes'])) {

            $query->whereHas('product', function ($query) use ($filters) {
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
