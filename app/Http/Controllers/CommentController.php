<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $keyword = $request->query('keyword', '');

        $query = Comment::query();

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('content', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                        $q->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        $comments = $query->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page)
            ->onEachSide(0)
            ->withQueryString();;

        return $this->paginationResponse(CommentResource::collection($comments), 'Comment get successfully');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Send comment to post.
     */
    public function send(CommentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;

        // Validasi parent_id jika ada
        if (isset($data['parent_id'])) {
            // Cek apakah parent_id valid dan tidak menunjuk ke dirinya sendiri
            $parentComment = Comment::find($data['parent_id']);

            if (!$parentComment) {
                return $this->errorResponse('Invalid parent comment.', 400);
            }

            // Cek apakah parent_id berada dalam post yang sama
            if ($parentComment->post_id !== $data['post_id']) {
                return $this->errorResponse('Parent comment must belong to the same post.', 400);
            }
        }
        //  else {
        //     // Jika tidak ada parent_id, pastikan itu adalah komentar utama
        //     if (Comment::where('post_id', $data['post_id'])->whereNull('parent_id')->exists()) {
        //         return $this->errorResponse('Parent ID is required for replies.', 400);
        //     }
        // }

        $comment = Comment::create($data);

        // $comment = Comment::create($data);
        return $this->successResponse(new CommentResource($comment), 'Comment send successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request) {}

    /**
     * Display the specified resource.
     */
    public function listCommentByPost(int $postId)
    {

        $comments = Comment::with(['replies' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return CommentResource::collection($comments)
            ->additional([
                'success' => true,
                'message' => 'Comments retrieved successfully',
                'meta' => [
                    'total' => $comments->count() + $comments->sum(function ($comment) {
                        return $comment->replies->count();
                    }),
                ],
            ]);
    }


    public function like(int $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return $this->errorResponse('Comment not found', 404);
        }

        if ($comment->likes()->where('user_id', auth()->user()->id)->where('comment_id', $id)->exists()) {
            $comment->likes()->where('user_id', auth()->user()->id)->delete();
            return $this->successResponse(true, 'Comment unliked successfully');
        }

        $comment->likes()->create([
            'user_id' => auth()->user()->id
        ]);

        return $this->successResponse(true, 'Comment liked successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $result = Comment::destroy($id);
        if (!$result) {
            return $this->errorResponse('Comment not found', 404);
        }
        return $this->successResponse(true, 'Comment deleted successfully');
    }
}
