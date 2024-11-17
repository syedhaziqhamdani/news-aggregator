<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;

class FetchArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_articles_command()
    {
        Article::truncate();
        $this->mock(\App\Services\ArticleAggregatorService::class, function ($mock) {
            $mock->shouldReceive('fetchAllArticles')
                 ->once()
                 ->andReturn([
                     [
                         'url' => 'https://example.com/article1',
                         'title' => 'Test Article 1',
                         'description' => 'Description for Test Article 1',
                         'source' => 'Test Source',
                         'publishedAt' => '2024-11-17T12:00:00Z',
                     ]
                 ]);
        });

        $this->artisan('fetch:articles')
             ->expectsOutput('Fetching articles...')
             ->expectsOutput('Articles fetched and stored successfully!')
             ->assertExitCode(0);

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article 1',
            'source' => 'Test Source',
        ]);

        $this->assertDatabaseCount('articles', 1);
    }
    public function test_articles_are_fetched_and_stored()
    {
        Article::truncate();
        // Mock the fetchAllArticles method in ArticleAggregatorService to return test data
        $this->mock(\App\Services\ArticleAggregatorService::class, function ($mock) {
            $mock->shouldReceive('fetchAllArticles')
                 ->once()
                 ->andReturn([
                     [
                         'url' => 'https://example.com/article1',
                         'title' => 'Test Article 1',
                         'description' => 'Description for test article 1',
                         'source' => ['name' => 'Test Source 1'],
                         'publishedAt' => '2024-11-17T12:00:00Z',
                     ],
                     [
                         'url' => 'https://example.com/article2',
                         'title' => 'Test Article 2',
                         'description' => 'Description for test article 2',
                         'source' => ['name' => 'Test Source 2'],
                         'publishedAt' => '2024-11-17T13:00:00Z',
                     ],
                 ]);
        });

        // Execute the command and check outputs
        $this->artisan('fetch:articles')
             ->expectsOutput('Fetching articles...')
             ->expectsOutput('Articles fetched and stored successfully!')
             ->assertExitCode(0);

        // Assert database contains fetched articles
        $this->assertDatabaseHas('articles', [
            'url' => 'https://example.com/article1',
            'title' => 'Test Article 1',
        ]);

        $this->assertDatabaseHas('articles', [
            'url' => 'https://example.com/article2',
            'title' => 'Test Article 2',
        ]);

        // Assert the count matches the mock data
        $this->assertDatabaseCount('articles', 2);
    }
}
