<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for a specific post (API).
     */
    public function index(Post $post): JsonResponse
    {
        return response()->json($post->comments()->latest()->get());
    }

    /**
     * Store a newly created comment in storage (Web / Blade).
     */
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $post->comments()->create($request->validated());

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Update the specified comment in storage (Web / Blade).
     */
    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $comment->update($request->validated());

        return back()->with('success', 'Komentar berhasil diperbarui!');
    }

    /**
     * Remove the specified comment from storage (Web / Blade).
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus!');
    }

    /**
     * Store a newly created comment in storage (API).
     */
    public function apiStore(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $post->comments()->create($request->validated());

        return response()->json($comment, 201);
    }

    /**
     * Update the specified comment in storage (API).
     */
    public function apiUpdate(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $comment->update($request->validated());

        return response()->json($comment);
    }

    /**
     * Remove the specified comment from storage (API).
     */
    public function apiDestroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }
}
