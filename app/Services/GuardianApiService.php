<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
        $this->baseUrl = config('services.guardian.base_url');
    }

    public function fetchArticles(array $params)
    {
        $cacheKey = 'guardian_articles:' . md5(json_encode($params));
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($params) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->get("{$this->baseUrl}/search", array_merge($params, [
                        'api-key' => $this->apiKey,
                    ]));

                if ($response->failed()) {
                    Log::error('Error fetching articles from The Guardian: ' . $response->body());
                    throw new \Exception('The Guardian API request failed: ' . $response->body());
                }

                $results = $response->json()['response']['results'] ?? [];
                Log::info('Fetched articles successfully from The Guardian', ['articles_count' => count($results)]);

                // Map API-specific structure to a common structure
                return array_map(function ($article) {
                    return [
                        'url' => $article['webUrl'] ?? '',
                        'title' => $article['webTitle'] ?? '',
                        'description' => $article['fields']['trailText'] ?? '',
                        'category' => $article['sectionName'] ?? '',
                        'publishedAt' => $article['webPublicationDate'] ?? '',
                    ];
                }, $results);
            } catch (\Exception $e) {
                Log::critical('An unexpected error occurred in GuardianApiService', [
                    'error_message' => $e->getMessage(),
                    'params' => $params,
                ]);
                throw $e;
            }
        });
    }
}
