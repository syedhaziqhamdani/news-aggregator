<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ArticleAggregatorService
{
    protected $newsApiService;
    protected $guardianApiService;
    protected $nytApiService;

    public function __construct(
        NewsApiService $newsApiService,
        GuardianApiService $guardianApiService,
        NytApiService $nytApiService
    ) {
        $this->newsApiService = $newsApiService;
        $this->guardianApiService = $guardianApiService;
        $this->nytApiService = $nytApiService;
    }

    public function fetchAllArticles(array $params)
    {
        $cacheKey = 'aggregated_articles:' . md5(json_encode($params));
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($params) {
            $articles = [];

            // Fetch articles from NewsAPI
            try {
                Log::info('Fetching articles from NewsAPI...');
                $newsApiArticles = $this->newsApiService->fetchArticles($params);
                foreach ($newsApiArticles as $article) {
                    $articles[] = [
                        'url' => $article['url'] ?? null,
                        'title' => $article['title'] ?? null,
                        'description' => $article['description'] ?? null,
                        'source' => $article['source']['name'] ?? '',
                        'published_at' => $article['publishedAt'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching from NewsAPI', ['error' => $e->getMessage()]);
            }

            // Fetch articles from The Guardian
            try {
                Log::info('Fetching articles from The Guardian...');
                $guardianArticles = $this->guardianApiService->fetchArticles($params);
                foreach ($guardianArticles as $article) {
                    $articles[] = [
                        'url' => $article['webUrl'] ?? null,
                        'title' => $article['webTitle'] ?? null,
                        'description' => $article['fields']['trailText'] ?? null,
                        'source' => 'The Guardian',
                        'published_at' => $article['webPublicationDate'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching from The Guardian', ['error' => $e->getMessage()]);
            }

            // Fetch articles from The New York Times
            try {
                Log::info('Fetching articles from The New York Times...');
                $nytArticles = $this->nytApiService->fetchArticles($params);
                foreach ($nytArticles as $article) {
                    $articles[] = [
                        'url' => $article['web_url'] ?? null,
                        'title' => $article['headline']['main'] ?? null,
                        'description' => $article['snippet'] ?? null,
                        'source' => 'New York Times',
                        'published_at' => $article['pub_date'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching from The New York Times', ['error' => $e->getMessage()]);
            }

            Log::info('Total articles fetched from all sources', ['total_count' => count($articles)]);

            return $articles;
        });
    }
}
