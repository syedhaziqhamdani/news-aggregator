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
        $response = Http::withOptions(['verify' => false])
            ->get("{$this->baseUrl}/everything", array_merge($params, [
                'apiKey' => $this->apiKey,
            ]));

        if ($response->failed()) {
            throw new \Exception('NewsAPI request failed: ' . $response->body());
        }
        if ($response->failed()) {
            Log::error('Error fetching articles from NewsAPI: ' . $response->body());
            throw new \Exception('NewsAPI request failed: ' . $response->body());
        }
    
        Log::info('Fetched articles successfully from NewsAPI', ['articles_count' => count($response->json()['articles'] ?? [])]);
        return $response->json()['articles'];
    }
}