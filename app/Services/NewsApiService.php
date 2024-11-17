<?php

namespace App\Services;

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

            if (is_null($articles)) {
                Log::warning('No articles key found in NewsAPI response', [
                    'response_body' => $response->body(),
                    'params' => $params,
                ]);
                throw new \Exception('NewsAPI response does not contain articles.');
            }

            Log::info('Fetched articles successfully from NewsAPI', [
                'articles_count' => count($articles),
                'params' => $params,
            ]);

            return collect($responseData['response']['docs'] ?? [])->map(function ($article) {
                return [
                    'title' => $article['headline']['main'] ?? null,
                    'description' => $article['snippet'] ?? null,
                    'source' => 'New York Times',
                    'url' => $article['web_url'] ?? null,
                    'published_at' => $article['pub_date'] ?? null,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::critical('An unexpected error occurred in NewsApiService', [
                'error_message' => $e->getMessage(),
                'params' => $params,
            ]);
            throw $e;
        }
    }
}
