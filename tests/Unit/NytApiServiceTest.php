<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\NytApiService;

class NytApiServiceTest extends TestCase
{
    public function test_fetch_articles_success()
    {
        Http::fake([
            'https://api.nytimes.com/*' => Http::response([
                'response' => [
                    'docs' => [
                        [
                            'web_url' => 'https://example.com/article1',
                            'headline' => ['main' => 'Test NYT Article'],
                            'abstract' => 'Test Abstract',
                            'pub_date' => '2024-11-17T12:00:00Z',
                        ],
                    ],
                ],
            ], 200),
        ]);
        
        $service = new NytApiService();
        $articles = $service->fetchArticles(['q' => 'technology']);
        
        $this->assertCount(1, $articles);
        $this->assertEquals('Test NYT Article', $articles[0]['title']);
        
    }

    public function test_fetch_articles_failure()
    {
        Http::fake([
            'https://api.nytimes.com/*' => Http::response([], 500),
        ]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('NYT API request failed:');
        
        $service = new NytApiService();
        $service->fetchArticles(['q' => 'technology']);
        
    }
}
