<?php

use App\Http\Controllers\CommentController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 1. READ (Daftar semua post)
Route::get('/posts', function () {
    return response()->json(Post::latest()->get());
});

// 2. CREATE (Tambah post baru)
Route::post('/posts', function (Request $request) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    $post = Post::create($validated);
    return response()->json($post, 201);
});

// 3. UPDATE (Ubah post)
Route::put('/posts/{post}', function (Request $request, Post $post) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    $post->update($validated);
    return response()->json($post);
});

// 4. DELETE (Hapus post)
Route::delete('/posts/{post}', function (Post $post) {
    $post->delete();
    return response()->json(['message' => 'Post berhasil dihapus']);
});

// --- API COMMENT ENDPOINTS ---
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
Route::post('/posts/{post}/comments', [CommentController::class, 'apiStore']);
Route::put('/comments/{comment}', [CommentController::class, 'apiUpdate']);
Route::delete('/comments/{comment}', [CommentController::class, 'apiDestroy']);
