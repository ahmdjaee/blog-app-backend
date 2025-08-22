<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookmarkController extends Controller
{
    /**
     * Store if bookmark doesnt exist and vice verca
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
        ]);

        // Check if the bookmark already exists
        $existingBookmark = Bookmark::where('post_id', $data['post_id'])
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            return $this->successResponse(false, 'Bookmark deleted successfully');
        }

        $data['user_id'] = auth()->user()->id;

        Bookmark::create($data);

        return $this->successResponse(true, 'Bookmark added successfully');
    }

    public function lists(Request $request): ResourceCollection
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $keyword = $request->query('keyword', '');

        $query = Post::query()->with('comments');

        if (!empty($keyword)) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function (Builder $q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })->orWhereHas('category', function (Builder $q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $query->where('published', 1)->with('bookmarks')->whereHas('bookmarks', function (Builder $q) {
            $q->where('user_id', auth()->user()->id);
        });


        $posts = $query->with('comments')->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page)
            ->onEachSide(0)
            ->withQueryString();

        return $this->paginationResponse(PostResource::collection($posts), 'Post get successfully');

    }
}
