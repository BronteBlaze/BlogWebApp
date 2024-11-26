<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Blogs API

Route::get('/blogs', [BlogController::class, 'index'])->name('get-blogs');

Route::middleware('auth:sanctum')->post('/blogs', [BlogController::class, 'store'])->name('post-blogs');

Route::middleware('auth:sanctum')->delete('/blogs/{id}', [BlogController::class, 'destroy'])->name('delete-blogs');

Route::middleware('auth:sanctum')->post('/blogs/{id}', [BlogController::class, 'update'])->name('update-blogs');

Route::get('/blogs/category/{category}', [BlogController::class, 'getBlogsByCategory'])->name('get-category-blogs');

Route::get('/blogs/months/{month}', [BlogController::class, 'getBlogsByMonth'])->name('get-month-blogs');

Route::get('/blog/{id}', [BlogController::class, 'show'])->name('get-blog-details');

Route::middleware('auth:sanctum')->post('/profile_pic', [BlogController::class, 'uploadProfilePicture'])->name('upload-profile-picture');

Route::middleware('auth:sanctum')->get('/your-blogs', [BlogController::class, 'yourBlogs'])->name('your-blogs');

Route::post('/blogs/views/{blog_id}', [BlogController::class, 'incrementView'])->name('increment-views');

// Notifications API

Route::middleware('auth:sanctum')->get('/notifications', [BlogController::class, 'getNotifications'])->name('get-notifications');
Route::middleware('auth:sanctum')->post('/blogs/status/{id}', [BlogController::class, 'updateStatus'])->name('update-blog-status');


// Comments API

Route::get('/comments/{blog_id}', [CommentController::class, 'show'])->name('show-comments');

Route::middleware('auth:sanctum')->post('/comments/{blog_id}', [CommentController::class, 'store'])->name('store-comments');

Route::middleware('auth:sanctum')->delete('/comments/{id}', [CommentController::class, 'destroy'])->name('delete-comment');


// Likes API
Route::middleware('auth:sanctum')->post('/likes/{blog_id}', [LikeController::class, 'toogleLike'])->name('toogle-likes');
Route::middleware('auth:sanctum')->get('/likes/{blog_id}', [LikeController::class, 'index'])->name('get-likes');
