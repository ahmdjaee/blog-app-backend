<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserPostController extends Controller
{
    public function listDrafts()
    {
        return $this->listByStatus(0);
    }

    public function listPublished()
    {
        return $this->listByStatus(1);
    }

    public function listComments()
    {
        $userId = auth('sanctum')->id();

        $comments = Comment::with('post')->whereHas('post', function (Builder $q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->latest()
            ->simplePaginate();

        return CommentResource::collection($comments)->additional($this->metaData("Comments", $userId));
    }


    /*-----------------------------------------
     * Helper section 
     *-----------------------------------------*/
    private function listByStatus(int $status)
    {
        $userId = auth('sanctum')->id();

        $posts = Post::where('user_id', $userId)
            ->where('published', $status)
            ->latest()
            ->simplePaginate();

        return PostResource::collection($posts)->additional($this->metaData("Posts", $userId));
    }


    private function metaData(string $name, int $userId)
    {
        $counts = Post::selectRaw('published, count(*) as total')
            ->where('user_id', $userId)
            ->groupBy('published')
            ->pluck('total', 'published');

        return [
            'success' => true,
            'message' => $name . 'get successfully',
            'meta' => [
                'total_drafts' => $counts[0] ?? 0,
                'total_published' => $counts[1] ?? 0,
                'total_comments' => Comment::whereHas('post', fn(Builder $q) => $q->where('user_id', $userId))->count(),
            ],
        ];
    }
}
