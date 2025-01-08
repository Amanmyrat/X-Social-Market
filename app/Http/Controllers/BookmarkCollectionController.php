<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\BookmarkCollectionResource;
use App\Models\BookmarkCollection;
use App\Services\PostBookmarkService;
use App\Traits\HandlesUserPostInteractions;
use App\Traits\PreparesPostQuery;
use App\Transformers\PostTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookmarkCollectionController extends ApiBaseController
{
    use HandlesUserPostInteractions, PreparesPostQuery;

    public function __construct(protected PostBookmarkService $service)
    {
        parent::__construct();
    }

    /**
     * List all collections
     */
    public function index(): AnonymousResourceCollection
    {
        $collections = BookmarkCollection::where('user_id', Auth::id())
            ->with([
                'bookmarks' => function ($query) {
                    $query->with('post')->limit(4);
                }
            ])
            ->get();

        return BookmarkCollectionResource::collection($collections);
    }

    /**
     * Create a new collection
     */
    public function store(Request $request): BookmarkCollectionResource
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $collection = BookmarkCollection::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
        ]);

        return new BookmarkCollectionResource($collection);
    }

    /**
     * Delete a collection
     */
    public function destroy(BookmarkCollection $collection): JsonResponse
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403, 'You do not own this collection.');
        }

        $collection->delete();

        return new JsonResponse([
            'message' => 'Collection deleted successfully',
        ]);
    }

    /**
     * Return all bookmark posts of collection
     */
    public function bookmarks(BookmarkCollection $collection): JsonResponse
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403, 'You do not own this collection.');
        }
        $bookmarks = $collection->bookmarks()->with('post')->get();
        $posts = $bookmarks->pluck('post');

        $userInteractionsDTO = $this->getUserInteractionsDTO();

        return $this->respondWithCollection($posts, new PostTransformer($userInteractionsDTO));
    }
}
