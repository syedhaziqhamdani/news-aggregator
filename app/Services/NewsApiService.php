<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        $this->baseUrl = config('services.newsapi.base_url');
    }

    public function fetchArticles(array $params)
    {
        $cacheKey = 'newsapi:' . md5(json_encode($params));
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($params) {

            try {
                $response = Http::withOptions(['verify' => false])
                    ->get("{$this->baseUrl}/everything", array_merge($params, [
                        'apiKey' => $this->apiKey,
                    ]));

                if ($response->failed()) {
                    Log::error('Error fetching articles from NewsAPI', [
                        'response_status' => $response->status(),
                        'response_body' => $response->body(),
                        'params' => $params,
                    ]);
                    throw new \Exception('NewsAPI request failed with status ' . $response->status());
                }

                $responseData = $response->json();

                if (!isset($responseData['articles'])) {
                    Log::warning('No articles key found in NewsAPI response', [
                        'response_body' => $response->body(),
                        'params' => $params,
                    ]);
                    throw new \Exception('NewsAPI response does not contain articles.');
                }

                $articles = $responseData['articles'];

                Log::info('Fetched articles successfully from NewsAPI', [
                    'articles_count' => count($articles),
                    'params' => $params,
                ]);

                // Map the articles into a consistent format
                return array_map(function ($article) {
                    return [
                        'title' => $article['title'] ?? null,
                        'description' => $article['description'] ?? null,
                        'source' => $article['source']['name'] ?? 'Unknown Source',
                        'url' => $article['url'] ?? null,
                        'published_at' => $article['publishedAt'] ?? null,
                    ];
                }, $articles);
            } catch (\Exception $e) {
                Log::critical('An unexpected error occurred in NewsApiService', [
                    'error_message' => $e->getMessage(),
                    'params' => $params,
                ]);
                throw $e;
            }
        });
    }
}
