<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class NytApiServiceTest extends TestCase
{
    public function test_fetch_articles_success()
    {
        Http::fake([
            'https://api.nytimes.com/svc/search/v2/articlesearch.json*' => Http::response([
                'response' => [
                    'docs' => [
                        [
                            'web_url' => 'https://example.com/article1',
                            'headline' => ['main' => 'Test NYT Article'],
                            'abstract' => 'Test Abstract',
                            'byline' => ['original' => 'Test Author'],
                            'pub_date' => '2024-11-17T12:00:00Z',
                            'source' => 'The New York Times',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new \App\Services\NytApiService();
        $articles = $service->fetchArticles(['q' => 'technology']);

        $this->assertNotEmpty($articles);
        $this->assertEquals('Test NYT Article', $articles[0]['title']);
    }

    public function test_fetch_articles_failure()
    {
        Http::fake([
            'https://api.nytimes.com/svc/search/v2/articlesearch.json*' => Http::response([], 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('NYT API request failed');

        $service = new \App\Services\NytApiService();
        $service->fetchArticles(['q' => 'technology']);
    }
}
