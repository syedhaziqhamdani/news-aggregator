<?php
namespace App\Services;

class ArticleAggregatorService
{
    protected $newsApiService;
    protected $theGuardianService;
    protected $newYorkTimesService;

    public function __construct(
        NewsApiService $newsApiService,
        TheGuardianService $theGuardianService,
        NewYorkTimesService $newYorkTimesService
    ) {
        $this->newsApiService = $newsApiService;
        $this->theGuardianService = $theGuardianService;
        $this->newYorkTimesService = $newYorkTimesService;
    }

    public function fetchAllArticles()
    {
        $articles = [];

        // Fetch articles from each API
        $articles[] = $this->newsApiService->fetchArticles(['q' => 'technology', 'language' => 'en', 'pageSize' => 10]);

        $guardianResponse = $this->theGuardianService->fetchArticles(['q' => 'technology', 'show-fields' => 'all']);
        $articles[] = $guardianResponse['response']['results'] ?? []; // Extract articles from The Guardian response

        $nytResponse = $this->newYorkTimesService->fetchArticles(['q' => 'technology']);
        $articles[] = $nytResponse['response']['docs'] ?? []; // Extract articles from New York Times response

        // Merge all responses and return
        return array_merge(...$articles);
    }
}

