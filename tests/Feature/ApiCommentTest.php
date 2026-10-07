<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_fetch_comments_for_a_post(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(2)->create(['post_id' => $post->id]);

        $response = $this->getJson("/api/posts/{$post->id}/comments");

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => ['id', 'post_id', 'author_name', 'content', 'created_at', 'updated_at'],
            ]);
    }

    public function test_api_can_create_a_comment(): void
    {
        $post = Post::factory()->create();

        $payload = [
            'author_name' => 'Siti Nurhaliza',
            'content' => 'Komentar via API.',
        ];

        $response = $this->postJson("/api/posts/{$post->id}/comments", $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'post_id' => $post->id,
                'author_name' => 'Siti Nurhaliza',
                'content' => 'Komentar via API.',
            ]);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'author_name' => 'Siti Nurhaliza',
        ]);
    }

    public function test_api_can_update_a_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $payload = [
            'author_name' => 'Penulis Revisi',
            'content' => 'Isi revisi via API.',
        ];

        $response = $this->putJson("/api/comments/{$comment->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $comment->id,
                'author_name' => 'Penulis Revisi',
                'content' => 'Isi revisi via API.',
            ]);
    }

    public function test_api_can_delete_a_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Komentar berhasil dihapus']);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
