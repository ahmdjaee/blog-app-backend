<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\User\StatisticController;
use App\Http\Controllers\User\UserPostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/register', 'register');
    Route::post('/auth/login', 'login');
});

Route::get('/recommendations', [RecommendationController::class, 'lists']);

Route::get('/posts', [PostController::class, 'list']);
Route::get('/posts/popular', [PostController::class, 'listPopularPost']);
Route::get('/posts/{slug}', [PostController::class, 'single']);

Route::get('/categories', [CategoryController::class, 'list']);
Route::get('/categories/{slug}', [CategoryController::class, 'single']);
Route::get('/comments/{postId}', [CommentController::class, 'listCommentByPost']);
Route::get('/comments', [CommentController::class, 'list']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments', [CommentController::class, 'send']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::post('/comments/{id}/like', [CommentController::class, 'like']);

    // Route::controller(SummaryController::class)->group(function () {
    //     Route::get('/summary/total-views', 'totalViews');
    //     Route::get('/summary/total-posts', 'totalPosts');
    //     Route::get('/summary/total-comments', 'totalComments');
    //     Route::get('/summary/published-posts', 'publishedPosts');
    // });

    Route::controller(PostController::class)->group(function () {
        Route::post('/posts', 'store');
        Route::put('/posts/{id}', 'update');
        Route::delete('/posts/{id}', 'destroy');
        Route::post('/posts/{id}/like', 'like');
    });

    Route::controller(UserPostController::class)->prefix('user')->group(function () {
        Route::get('/posts/drafts', 'listDrafts');
        Route::get('/posts/published', 'listPublished');
        Route::get('/posts/comments', 'listComments');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::delete('/auth/logout', 'logout');
        Route::get('/auth/me', 'me');
    });

    Route::controller(BookmarkController::class)->group(function () {
        Route::get('/bookmarks', 'lists');
        Route::post('/bookmarks/toggle', 'toggle');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'profile');
        Route::patch('/profile', 'update');
        Route::post('/profile/socials', 'social');
        Route::post('/profile/update-avatar', 'updateAvatar');
    });

    Route::controller(StatisticController::class)->group(function () {
        Route::get('/user/stats/views', 'views');
        Route::get('/user/stats/summary', 'summary');
    });
});

/**------------------------------------------------------------
 * ADMIN ROUTES
 *-------------------------------------------------------------*/

Route::middleware(['auth:sanctum', EnsureIsAdmin::class])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'list');
        Route::post('/users', 'store');
        Route::patch('/users/{id}', 'update');
        Route::delete('/users/{id}', 'destroy');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::post('/categories', 'store');
        Route::put('/categories/{id}', 'update');
        Route::delete('/categories/{id}', 'destroy');
    });

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/admin/dashboard/summary', 'summary');
        Route::get('/admin/dashboard/latest-posts', 'latestPosts');
        Route::get('/admin/dashboard/latest-users', 'latestUsers');
    });

    Route::controller(RecommendationController::class)->group(function () {
        Route::delete('/recommendations/{id}',  'destroy');
        Route::post('/recommendations',  'store');
    });

});
