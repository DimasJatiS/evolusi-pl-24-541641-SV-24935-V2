<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Public Endpoints ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/posts', function () {
    return response()->json(Post::latest()->get(), 200); // 200 OK
});

// --- Comment Public Endpoints ---
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
Route::post('/posts/{post}/comments', [CommentController::class, 'apiStore']);
Route::put('/comments/{comment}', [CommentController::class, 'apiUpdate']);
Route::delete('/comments/{comment}', [CommentController::class, 'apiDestroy']);

// --- Protected Endpoints (auth:sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user(), 200);
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD Create
    Route::post('/posts', function (Request $request) {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]); // Otomatis return 422 Unprocessable Content jika gagal
        $post = Post::create($validated);
        return response()->json($post, 201); // 201 Created
    });

    // CRUD Update
    Route::put('/posts/{post}', function (Request $request, Post $post) {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $post->update($validated);
        return response()->json($post, 200); // 200 OK
    });

    // CRUD Delete
    Route::delete('/posts/{post}', function (Post $post) {
        $post->delete();
        return response()->json(['message' => 'Post berhasil dihapus'], 200); // 200 OK
    });
});
