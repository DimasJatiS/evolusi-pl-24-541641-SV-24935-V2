<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_posts_returns_successful_json_response(): void
    {
        Post::factory()->create([
            'title' => 'Judul Post Pertama',
            'content' => 'Konten postingan pertama untuk testing API.',
        ]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'content',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'title' => 'Judul Post Pertama',
            ]);
    }
}
