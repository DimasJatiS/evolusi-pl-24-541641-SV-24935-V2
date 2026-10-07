<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_comment_on_a_post(): void
    {
        $post = Post::factory()->create();

        $payload = [
            'author_name' => 'Budi Santoso',
            'content' => 'Artikel ini sangat bermanfaat!',
        ];

        $response = $this->post(route('posts.comments.store', $post), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'author_name' => 'Budi Santoso',
            'content' => 'Artikel ini sangat bermanfaat!',
        ]);
    }

    public function test_cannot_store_comment_with_missing_fields(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(route('posts.comments.store', $post), [
            'author_name' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['author_name', 'content']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_can_update_a_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'author_name' => 'Nama Lama',
            'content' => 'Komentar Lama',
        ]);

        $payload = [
            'author_name' => 'Nama Baru',
            'content' => 'Komentar yang telah diperbarui.',
        ];

        $response = $this->put(route('comments.update', $comment), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'author_name' => 'Nama Baru',
            'content' => 'Komentar yang telah diperbarui.',
        ]);
    }

    public function test_can_delete_a_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }
}
