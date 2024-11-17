<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

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

        $service = new \App\Services\GuardianApiService();
        $articles = $service->fetchArticles(['q' => 'technology']);

        $this->assertNotEmpty($articles);
        $this->assertEquals('Test Guardian Article', $articles[0]['title']);
    }

    public function test_fetch_articles_failure()
    {
        Http::fake([
            'https://content.guardianapis.com/search*' => Http::response([], 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The Guardian API request failed');

        $service = new \App\Services\GuardianApiService();
        $service->fetchArticles(['q' => 'technology']);
    }
}
