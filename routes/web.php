<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

Route::get('/details/{id}', function () {
    return file_get_contents(public_path('react-app/index.html'));
});

Route::get('/{any?}', function () {
    return file_get_contents(public_path('react-app/index.html'));
})->where('any', '^(signup|signin|write-blog|
edit-blog|admin|details|category|archieves|user|user/profile|user/blogs)$');

require __DIR__ . '/auth.php';

Route::middleware('auth:sanctum')->get('/share/blog/{id}', [BlogController::class, 'share'])->name('share-blog');
