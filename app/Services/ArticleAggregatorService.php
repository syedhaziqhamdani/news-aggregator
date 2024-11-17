<?php

namespace App\Services;
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
        $articles = [];

        // Fetch articles from NewsAPI
        try {
            Log::info('Fetching articles from NewsAPI...');
            $newsApiArticles = $this->newsApiService->fetchArticles($params);
            $articles = array_merge($articles, $newsApiArticles);
        } catch (\Exception $e) {
            \Log::error('Error fetching from NewsAPI', ['error' => $e->getMessage()]);
        }

        // Fetch articles from The Guardian
        try {
            Log::info('Fetching articles from The Guardian...');
            $guardianArticles = $this->guardianApiService->fetchArticles($params);
            $articles = array_merge($articles, $guardianArticles);
        } catch (\Exception $e) {
            \Log::error('Error fetching from The Guardian', ['error' => $e->getMessage()]);
        }

        // Fetch articles from The New York Times
        try {
            Log::info('Fetching articles from The New York Times...');
            $nytArticles = $this->nytApiService->fetchArticles($params);
            $articles = array_merge($articles, $nytArticles);
        } catch (\Exception $e) {
            \Log::error('Error fetching from The New York Times', ['error' => $e->getMessage()]);
        }

        Log::info('Total articles fetched from all sources', ['total_count' => count($articles)]);

        
        return $articles;
    }
}
