<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;
use App\Models\User;

class ArticleTest extends TestCase
{
    use RefreshDatabase;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $user = User::factory()->create();
        $this->token = $user->createToken('Test Token')->plainTextToken;
    }

    public function test_can_fetch_articles()
    {
        Article::factory()->count(10)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson('/api/articles');

        // Dump the response for debugging
        // $response->dump();

        $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_filter_articles_by_keyword()
    {
        Article::factory()->create(['title' => 'Breaking News']);
        Article::factory()->create(['title' => 'Another Article']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson('/api/articles?keyword=Breaking');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_articles_by_category()
    {
        Article::factory()->create(['category' => 'Technology']);
        Article::factory()->create(['category' => 'Health']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson('/api/articles?category=Technology');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_can_view_single_article()
    {
        $article = Article::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson("/api/articles/{$article->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'title', 'description', 'url', 'source', 'category']);
    }
}
