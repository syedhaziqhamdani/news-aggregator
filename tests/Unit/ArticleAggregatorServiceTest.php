<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ArticleAggregatorService;

class ArticleAggregatorServiceTest extends TestCase
{
    public function test_fetch_all_articles_success()
    {
        // Mock services to return predefined articles
        $this->mock(\App\Services\NewsApiService::class, function ($mock) {
            $mock->shouldReceive('fetchArticles')
                ->with(['q' => 'technology'])
                ->andReturn([
                    ['title' => 'Test Article 1', 'source' => ['name' => 'Test Source']],
                ]);
        });

        $this->mock(\App\Services\GuardianApiService::class, function ($mock) {
            $mock->shouldReceive('fetchArticles')
                ->with(['q' => 'technology'])
                ->andReturn([]);
        });

        $this->mock(\App\Services\NytApiService::class, function ($mock) {
            $mock->shouldReceive('fetchArticles')
                ->with(['q' => 'technology'])
                ->andReturn([]);
        });

        $service = app(\App\Services\ArticleAggregatorService::class);
        $articles = $service->fetchAllArticles(['q' => 'technology']);

        $this->assertCount(1, $articles);
        $this->assertEquals('Test Article 1', $articles[0]['title']);
    }
}
