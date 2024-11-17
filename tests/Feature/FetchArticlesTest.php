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
        // Clear the articles table to ensure a clean slate
        Article::truncate();

        // Mock the ArticleAggregatorService
        $this->mock(\App\Services\ArticleAggregatorService::class, function ($mock) {
            $mock->shouldReceive('fetchAllArticles')
                ->once()
                ->andReturn([
                    [
                        'url' => 'https://example.com/article1',
                        'title' => 'Test Article 1',
                        'description' => 'Description for Test Article 1',
                        'source' => ['name' => 'Test Source 1'], // Updated to match service structure
                        'publishedAt' => '2024-11-17T12:00:00Z',
                    ],
                ]);
        });

        // Run the fetch:articles command
        $this->artisan('fetch:articles')
            ->expectsOutput('Fetching articles...')
            ->expectsOutput('Articles fetched and stored successfully!')
            ->assertExitCode(0);

        // Assert that the database contains the correct article
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article 1',
            'source' => 'Test Source 1', // Ensure source matches normalized value
        ]);

        // Ensure only one article is stored
        $this->assertDatabaseCount('articles', 1);
    }

    public function test_articles_are_fetched_and_stored()
    {
        // Clear the articles table to ensure a clean slate
        Article::truncate();

        // Mock the ArticleAggregatorService
        $this->mock(\App\Services\ArticleAggregatorService::class, function ($mock) {
            $mock->shouldReceive('fetchAllArticles')
                ->once()
                ->andReturn([
                    [
                        'url' => 'https://example.com/article1',
                        'title' => 'Test Article 1',
                        'description' => 'Description for Test Article 1',
                        'source' => ['name' => 'Test Source 1'], // Updated to match service structure
                        'publishedAt' => '2024-11-17T12:00:00Z',
                    ],
                    [
                        'url' => 'https://example.com/article2',
                        'title' => 'Test Article 2',
                        'description' => 'Description for Test Article 2',
                        'source' => ['name' => 'Test Source 2'], // Updated to match service structure
                        'publishedAt' => '2024-11-17T13:00:00Z',
                    ],
                ]);
        });

        // Run the fetch:articles command
        $this->artisan('fetch:articles')
            ->expectsOutput('Fetching articles...')
            ->expectsOutput('Articles fetched and stored successfully!')
            ->assertExitCode(0);

        // Assert that the database contains the first article
        $this->assertDatabaseHas('articles', [
            'url' => 'https://example.com/article1',
            'title' => 'Test Article 1',
            'source' => 'Test Source 1', // Ensure source matches normalized value
        ]);

        // Assert that the database contains the second article
        $this->assertDatabaseHas('articles', [
            'url' => 'https://example.com/article2',
            'title' => 'Test Article 2',
            'source' => 'Test Source 2', // Ensure source matches normalized value
        ]);

        // Ensure two articles are stored
        $this->assertDatabaseCount('articles', 2);
    }
}
