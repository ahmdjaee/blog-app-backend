<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $user = User::where('role', "!=", "admin", )->count();
        $post = Post::where('published', 1)->count();
        $category = Category::count();

        return $this->successResponse([
            'total_users' => $user,
            'total_posts' => $post,
            'total_categories' => $category,
        ], 'User get successfully', );
    }

    public function latestPosts(): JsonResponse
    {
        $posts = Post::where('published', 1)->latest()->limit(5)->get();

        return $this->successResponse(PostResource::collection($posts), 'Posts get successfully');
    }

    public function latestUsers(): JsonResponse
    {
        $users = User::where('role', "!=", "admin", )->latest()->limit(5)->get();

        return $this->successResponse(UserResource::collection($users)->each->withCreatedAt(), 'Users get successfully');
    }
}
