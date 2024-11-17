<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\GuardianApiService;

class GuardianApiServiceTest extends TestCase
{
    public function test_fetch_articles_success()
    {
        Http::fake([
            'https://content.guardianapis.com/search*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'webUrl' => 'https://example.com/article1',
                            'webTitle' => 'Test Guardian Article',
                            'fields' => ['trailText' => 'Test Abstract'],
                            'sectionName' => 'Technology',
                            'webPublicationDate' => '2024-11-17T12:00:00Z',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GuardianApiService();
        $articles = $service->fetchArticles(['q' => 'technology']);

        $this->assertNotEmpty($articles);
        $this->assertEquals('Test Guardian Article', $articles[0]['title']);
        $this->assertEquals('https://example.com/article1', $articles[0]['url']);
    }

    public function test_fetch_articles_failure()
    {
        Http::fake([
            'https://content.guardianapis.com/search*' => Http::response([], 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The Guardian API request failed');

        $service = new GuardianApiService();
        $service->fetchArticles(['q' => 'technology']);
    }
}
