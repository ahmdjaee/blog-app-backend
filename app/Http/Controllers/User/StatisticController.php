<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function views()
    {
        $totalViews = auth()->user()->total_views;

        $views = DB::table('post_views')
            ->join('posts', 'post_views.post_id', '=', 'posts.id')
            ->where('posts.user_id', auth()->id())
            ->where('post_views.created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE_FORMAT(post_views.created_at, "%d %b") as day'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('day')
            ->orderByRaw('MIN(post_views.created_at)') // supaya urut
            // ->orderBy('day')
            ->get();

        return response()->json([
            'data' => $views,
            'total_views' => $totalViews
        ]);
    }

    public function summary(Request $request)
    {
        $userId = auth()->id();

        // Hitung total komentar berdasarkan post milik user yang sedang login
        $totalComments = Comment::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $totalLikes = Like::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        return $this->successResponse([
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
        ], 'Post count retrieved successfully');
    }
}
