<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase; // Memastikan migrasi database dijalankan otomatis di memori test

    public function test_can_render_posts_index_page(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
        $response->assertViewHas('posts');
    }

    public function test_can_render_posts_create_page(): void
    {
        $response = $this->get(route('posts.create'));

        $response->assertStatus(200);
        $response->assertViewIs('posts.create');
    }

    public function test_can_store_a_valid_post(): void
    {
        $payload = [
            'title' => 'Judul Artikel Baru',
            'content' => 'Konten lengkap untuk artikel baru.',
            'is_published' => true,
        ];

        $response = $this->post(route('posts.store'), $payload);

        $response->assertRedirect(route('posts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'title' => 'Judul Artikel Baru',
            'is_published' => 1,
        ]);
    }

    public function test_cannot_store_post_with_invalid_data(): void
    {
        $response = $this->post(route('posts.store'), [
            'title' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content']);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_can_render_post_show_page(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertSee($post->title);
    }

    public function test_can_render_post_edit_page(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.edit', $post));

        $response->assertStatus(200);
        $response->assertViewIs('posts.edit');
        $response->assertSee($post->title);
    }

    public function test_can_update_an_existing_post(): void
    {
        $post = Post::factory()->create([
            'title' => 'Judul Lama',
        ]);

        $payload = [
            'title' => 'Judul Baru yang Diperbarui',
            'content' => 'Konten yang sudah diperbarui.',
            'is_published' => false,
        ];

        $response = $this->put(route('posts.update', $post), $payload);

        $response->assertRedirect(route('posts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Judul Baru yang Diperbarui',
            'is_published' => 0,
        ]);
    }

    public function test_can_delete_a_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }
}
