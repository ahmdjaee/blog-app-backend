<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    /**
     * Admin dashboard.
     */





    /**
     * User dashboard.
     */

    public function totalViews(Request $request)
    {
        $query = Post::query();

        $userId = auth()->user()->id;

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        $post = $query->sum('view_count');

        return $this->successResponse($post, 'Post get successfully');
    }

    public function totalPosts(Request $request)
    {
        $query = Post::query();

        $userId = auth()->user()->id;

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        $post = $query->count();

        return $this->successResponse($post, 'Post get successfully');
    }

    public function totalComments(Request $request)
    {
        $userId = auth()->id();

        // Hitung total komentar berdasarkan post milik user yang sedang login
        $totalComments = Comment::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        return $this->successResponse($totalComments, 'Post count retrieved successfully');
    }


    public function publishedPosts(Request $request)
    {
        $query = Post::query();

        $userId = auth()->user()->id;

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        $post = $query->where('published', true)->count();

        return $this->successResponse($post, 'Post get successfully');
    }
}
