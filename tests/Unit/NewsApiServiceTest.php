<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\NewsApiService;

class NewsApiServiceTest extends TestCase
{
    public function test_fetch_articles_success()
    {
        // Mock the HTTP client response for a successful request
        Http::fake([
            'https://newsapi.org/v2/everything*' => Http::response([
                'articles' => [
                    [
                        'title' => 'Test Article',
                        'description' => 'Test Description',
                        'url' => 'https://example.com/article1',
                        'source' => ['name' => 'Test Source'],
                        'publishedAt' => '2024-11-17T12:00:00Z',
                    ],
                ],
            ], 200),
        ]);

        // Test the service
        $service = new NewsApiService();
        $articles = $service->fetchArticles(['q' => 'technology']);

        // Validate the response
        $this->assertNotEmpty($articles);
        $this->assertEquals('Test Article', $articles[0]['title']);
        $this->assertEquals('https://example.com/article1', $articles[0]['url']);
    }

    public function test_fetch_articles_failure()
    {
        // Mock the HTTP client response for a failed request
        Http::fake([
            'https://newsapi.org/v2/everything*' => Http::response([], 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('NewsAPI request failed with status 500');

        // Test the service
        $service = new NewsApiService();
        $service->fetchArticles(['q' => 'technology']);
    }
}
