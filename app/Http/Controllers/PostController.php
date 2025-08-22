<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $request->validate(['thumbnail' => ['required', 'image', 'max:5024'],]);
        $data['user_id'] = auth()->user()->id;
        $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        $post = Post::create($data);
        return $this->successResponse(new PostResource($post), 'Post created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function list(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $keyword = $request->query('keyword', '');
        $category = $request->query('category', '');
        $userId = $request->query('user_id', '');
        $published = $request->query('published', );
        $query = Post::query()->with('comments');

        if (!empty($keyword)) {
            $query->where(function (Builder $q) use ($keyword, $category) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function (Builder $q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })->orWhereHas('category', function (Builder $q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if (!empty($category)) {
            $query->whereHas('category', function (Builder $q) use ($category) {
                $q->where('slug', 'like', "%{$category}%");
            });
        }

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        if (isset($published)) {
            $query->where('published', $published);
        }

        $posts = $query->with(['comments', 'user'])->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page)
            ->onEachSide(0)
            ->withQueryString();

        return $this->paginationResponse(PostResource::collection($posts), 'Post get successfully');
    }

    public function listPopularPost(Request $request)
    {
        $query = Post::query();
        $userId = $request->query('user_id', '');

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        $posts = $query->orderBy('view_count', 'desc')
            ->where('published', true)
            // ->where('published_at', '>', Carbon::now()->subDays(7))
            ->withCount('comments')
            ->limit(3)
            ->get();

        return $this->successResponse(PostResource::collection($posts), 'Post get successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, int $id)
    {
        $data = $request->validated();

        $post = Post::find($id);
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail != 'thumbnails/image.png') {
                Storage::disk('public')->delete("$post->thumbnail");
            }
            $request->validate(['thumbnail' => ['required', 'image', 'max:5024'],]);
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $post->update($data);
        return $this->successResponse(new PostResource($post), 'Post updated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function single(string $slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }

        PostView::updateOrCreate([
            'post_id' => $post->id,
            'ip_address' => request()->ip(),
        ]);

        $post->loadCount('views');

        // info("View COUNT - " . $post->views_count);
        return $this->successResponse(new PostResource($post), 'Post get successfully');
    }

    /**
     * Display the specified resource.
     */
    public function like(int $id)
    {
        $post = Post::where('id', $id)->first();

        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }

        if ($post->likes()->where('user_id', auth()->user()->id)->exists()) {
            $post->likes()->where('user_id', auth()->user()->id)->delete();
            return $this->successResponse(true, 'Post unliked successfully');
        }

        $post->likes()->create([
            'user_id' => auth()->user()->id
        ]);

        return $this->successResponse(true, 'Post liked successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $result = Post::find($id);
        if (!$result) {
            return $this->errorResponse('Post not found', 404);
        }
        $result->delete();
        if ($result->thumbnail) {
            if ($result->thumbnail != 'thumbnails/image.png') {
                Storage::disk('public')->delete($result->thumbnail);

            }
        }
        return $this->successResponse(true, 'Post deleted successfully');
    }
}
