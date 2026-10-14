<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_posts_publicly(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_unauthenticated_user_cannot_create_post(): void
    {
        $response = $this->postJson('/api/posts', [
            'title'   => 'Judul Tes',
            'content' => 'Konten Tes',
        ]);

        $response->assertStatus(401); // 401 Unauthorized
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', [
            'title'   => 'Judul Baru dari API',
            'content' => 'Isi postingan baru berhasil disimpan.',
        ]);

        $response->assertStatus(201) // 201 Created
                 ->assertJsonFragment(['title' => 'Judul Baru dari API']);
    }

    public function test_create_post_validation_fails_with_422(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', [
            'title'   => '',
            'content' => '',
        ]);

        $response->assertStatus(422) // 422 Unprocessable Content
                 ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_authenticated_user_can_update_post(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $post = Post::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/posts/{$post->id}", [
            'title'   => 'Judul Telah Diperbarui',
            'content' => 'Konten diperbarui via API.',
        ]);

        $response->assertStatus(200) // 200 OK
                 ->assertJsonFragment(['title' => 'Judul Telah Diperbarui']);
    }

    public function test_authenticated_user_can_delete_post(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $post = Post::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200) // 200 OK
                 ->assertJson(['message' => 'Post berhasil dihapus']);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
